<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PushMessageNotification;
use Illuminate\Console\Command;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Event;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class TestFcmSend extends Command
{
    protected $signature = 'fcm:test';

    protected $description = 'Send a test FCM message and diagnose errors';

    public function handle(Messaging $messaging): int
    {
        $user = User::whereNotNull('fcm_token')->first();

        if (! $user) {
            $this->error('No user with an FCM token found in the database.');
            return self::FAILURE;
        }

        $this->line("User   : #{$user->id} ({$user->email})");
        $this->line("Token  : " . substr($user->fcm_token, 0, 40) . '...');
        $this->newLine();

        // ── 1. Raw kreait send ──────────────────────────────────────────────
        $this->info('Test 1 — Raw kreait Messaging::send()');
        try {
            $message = CloudMessage::withTarget('token', $user->fcm_token)
                ->withNotification(Notification::create('FCM Test', 'Hello from raw kreait!'));
            $messaging->send($message);
            $this->line('  ✅ SUCCESS');
        } catch (\Throwable $e) {
            $this->error('  ❌ FAILED: ' . get_class($e));
            $this->line('  Message : ' . $e->getMessage());
            $prev = $e->getPrevious();
            if ($prev) {
                $this->line('  Caused by: ' . get_class($prev) . ': ' . $prev->getMessage());
            }
        }

        $this->newLine();

        // ── 2. Channel send — listen for NotificationFailed ─────────────────
        $this->info('Test 2 — PushMessageNotification via FcmChannel');
        $channelError = null;

        Event::listen(NotificationFailed::class, function (NotificationFailed $event) use (&$channelError) {
            $report = $event->data['report'] ?? null;
            $channelError = $report ? $report->error()?->getMessage() : 'unknown channel failure';
        });

        try {
            $user->notifyNow(new PushMessageNotification('Channel Test', 'Via FcmChannel', ['type' => 'test']));

            if ($channelError) {
                $this->error('  ❌ NotificationFailed event fired: ' . $channelError);
            } else {
                $this->line('  ✅ No failure event — FCM delivered (or token was empty)');
            }
        } catch (\Throwable $e) {
            $this->error('  ❌ Exception thrown: ' . get_class($e));
            $this->line('  Message : ' . $e->getMessage());
        }

        $this->newLine();

        // ── 3. Credential validation ─────────────────────────────────────────
        $this->info('Test 3 — Service account file check');
        $path = config('firebase.credentials');
        $this->line("  Path     : {$path}");
        $this->line("  Readable : " . (is_readable($path) ? 'YES' : 'NO'));
        $json = json_decode(file_get_contents($path), true);
        $this->line("  type     : " . ($json['type'] ?? 'MISSING'));
        $this->line("  project  : " . ($json['project_id'] ?? 'MISSING'));
        $this->line("  email    : " . ($json['client_email'] ?? 'MISSING'));
        $this->line("  key_id   : " . ($json['private_key_id'] ?? 'MISSING'));
        $hasKey = !empty($json['private_key']) && str_contains($json['private_key'], 'BEGIN PRIVATE KEY');
        $this->line("  key PEM  : " . ($hasKey ? 'YES' : 'NO — key is empty or malformed!'));

        return self::SUCCESS;
    }
}
