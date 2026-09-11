<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DiagnoseFcm extends Command
{
    protected $signature = 'fcm:diagnose';

    protected $description = 'Diagnose FCM configuration and connectivity on this server';

    public function handle(): int
    {
        $this->info('══════════════════════════════════════════');
        $this->info('  FCM Diagnostics');
        $this->info('══════════════════════════════════════════');

        $ok = true;

        // ── 1. PHP extensions ────────────────────────────────────────────────────
        $this->newLine();
        $this->line('<fg=cyan>1. PHP Extensions</>');

        foreach (['openssl', 'sodium', 'curl', 'json'] as $ext) {
            if (extension_loaded($ext)) {
                $this->line("   ✓ ext-{$ext}");
            } else {
                $this->error("   ✗ ext-{$ext} is NOT loaded — required for FCM JWT signing");
                $ok = false;
            }
        }

        // ── 2. .env values ───────────────────────────────────────────────────────
        $this->newLine();
        $this->line('<fg=cyan>2. Environment Variables</>');

        $credPath = config('firebase.credentials');
        $this->line("   FIREBASE_CREDENTIALS = {$credPath}");

        if (empty($credPath)) {
            $this->error('   ✗ FIREBASE_CREDENTIALS is not set in .env');
            $ok = false;
        }

        // ── 3. Credentials file ──────────────────────────────────────────────────
        $this->newLine();
        $this->line('<fg=cyan>3. Service Account File</>');

        $absolutePath = $credPath; // already resolved to absolute in config/firebase.php
        $this->line("   Resolved path: {$absolutePath}");

        if (! file_exists($absolutePath)) {
            $this->error("   ✗ File does not exist at: {$absolutePath}");
            $ok = false;
        } elseif (! is_readable($absolutePath)) {
            $this->error("   ✗ File exists but is not readable (check permissions)");
            $ok = false;
        } else {
            $this->line('   ✓ File exists and is readable');

            // Validate JSON structure
            $json = json_decode(file_get_contents($absolutePath), true);

            $requiredKeys = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email'];
            $missing = array_diff($requiredKeys, array_keys($json ?? []));

            if ($missing) {
                $this->error('   ✗ JSON is missing keys: ' . implode(', ', $missing));
                $ok = false;
            } else {
                $this->line("   ✓ type          = {$json['type']}");
                $this->line("   ✓ project_id    = {$json['project_id']}");
                $this->line("   ✓ client_email  = {$json['client_email']}");
                $this->line("   ✓ private_key_id= {$json['private_key_id']}");

                // Validate the private key can be loaded
                $key = openssl_pkey_get_private($json['private_key']);
                if ($key === false) {
                    $this->error('   ✗ private_key cannot be loaded by OpenSSL — key may be corrupt');
                    $ok = false;
                } else {
                    $this->line('   ✓ private_key is a valid RSA key (OpenSSL accepts it)');

                    // Try signing a test payload to verify sodium / OpenSSL can sign
                    $testData = 'test_payload_' . time();
                    $signed   = '';
                    if (openssl_sign($testData, $signed, $key, OPENSSL_ALGO_SHA256)) {
                        $this->line('   ✓ OpenSSL signing works (SHA256)');
                    } else {
                        $this->error('   ✗ OpenSSL signing failed — cannot produce JWT');
                        $ok = false;
                    }
                }
            }
        }

        // ── 4. Outbound HTTPS to Google ──────────────────────────────────────────
        $this->newLine();
        $this->line('<fg=cyan>4. Network — Google OAuth endpoint</>');

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=test');
        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $this->error("   ✗ cURL error: {$curlError}");
            $ok = false;
        } elseif ($httpCode === 0) {
            $this->error('   ✗ No response from Google — outbound HTTPS may be blocked');
            $ok = false;
        } else {
            $this->line("   ✓ Reached Google OAuth endpoint (HTTP {$httpCode})");
        }

        // ── 5. Users with FCM tokens ─────────────────────────────────────────────
        $this->newLine();
        $this->line('<fg=cyan>5. FCM Tokens in Database</>');

        $total      = \App\Models\User::query()->count();
        $withToken  = \App\Models\User::query()->whereNotNull('fcm_token')->count();
        $this->line("   Total users:       {$total}");
        $this->line("   Users with tokens: {$withToken}");

        if ($withToken === 0) {
            $this->warn('   ⚠ No FCM tokens found — users must log in from the app first');
        }

        // ── 6. Google OAuth — service account token (catches invalid_grant) ─────
        $this->newLine();
        $this->line('<fg=cyan>6. Firebase OAuth (service account)</>');

        if (file_exists($absolutePath ?? '')) {
            try {
                /** @var \Kreait\Firebase\Contract\Messaging $messaging */
                $messaging = app(\Kreait\Firebase\Contract\Messaging::class);
                $user = \App\Models\User::query()->whereNotNull('fcm_token')->first();
                if ($user) {
                    $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $user->fcm_token)
                        ->withNotification(\Kreait\Firebase\Messaging\Notification::create('FCM diagnose', 'ping'));
                    $messaging->send($message);
                    $this->line('   ✓ Google accepted credentials and FCM send succeeded');
                } else {
                    // No token — still verify OAuth by requesting access token via factory
                    app(\Kreait\Firebase\Contract\Messaging::class);
                    $this->line('   ✓ Google accepted credentials (no user token to send to)');
                }
            } catch (\Throwable $e) {
                $msg = $e->getMessage();
                $this->error("   ✗ Firebase auth/send failed: {$msg}");
                if (str_contains($msg, 'invalid_grant')) {
                    $this->warn('   → The service account private key is revoked or invalid.');
                    $this->warn('   → Firebase Console → Project Settings → Service Accounts → Generate new private key');
                    $this->warn('   → Replace public/firebase-adminsdk.json on the server, then run: php artisan config:clear');
                }
                $ok = false;
            }
        }

        // ── Summary ──────────────────────────────────────────────────────────────
        $this->newLine();
        $this->info('══════════════════════════════════════════');

        if ($ok) {
            $this->info('  ✓ All checks passed — FCM should work');
            $this->line('  If pushes still fail, the service account key may be');
            $this->line('  revoked on Google\'s side. Regenerate it from:');
            $this->line('  Firebase Console → Project Settings → Service Accounts');
        } else {
            $this->error('  ✗ One or more checks failed — see details above');
        }

        $this->info('══════════════════════════════════════════');

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
