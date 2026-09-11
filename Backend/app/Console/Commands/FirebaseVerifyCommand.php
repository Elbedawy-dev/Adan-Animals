<?php

namespace App\Console\Commands;

use App\Services\FcmService;
use App\Support\FirebaseWebPush;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FirebaseVerifyCommand extends Command
{
    protected $signature = 'firebase:verify';

    protected $description = 'Verify Firebase service account JSON path and web push (.env) variables';

    public function handle(): int
    {
        $this->components->info('Firebase / FCM configuration check');
        $this->newLine();

        $credentialsFileOk = $this->verifyServiceAccount();
        $fcmReady = app(FcmService::class)->isConfigured();

        $this->newLine();
        $this->checkWebPush();

        return ($credentialsFileOk && $fcmReady) ? self::SUCCESS : self::FAILURE;
    }

    private function verifyServiceAccount(): bool
    {
        $path = config('firebase.credentials');

        if (! is_string($path) || $path === '') {
            $this->components->error('FIREBASE_CREDENTIALS is empty in .env.');
            $this->line('Laravel only reads variables from the .env file on this machine (not from chat).');
            $this->line('Save the JSON file in the project, then set for example:');
            $this->line('  FIREBASE_CREDENTIALS=storage/app/firebase-adminsdk.json');
            $this->line('Or an absolute path; on Windows prefer forward slashes, e.g. C:/keys/adan.json');

            return false;
        }

        $this->line('Resolved credentials path:');
        $this->line('  '.$path);

        if (str_contains(str_replace('\\', '/', $path), '/public/')) {
            $this->components->warn('Credentials live under public/: never commit this file; Apache should block HTTP access to firebase*.json (see public/.htaccess). Prefer storage/app/ in production.');
        }

        if (! File::isFile($path)) {
            $this->components->error('That path is not a file. Fix FIREBASE_CREDENTIALS or move the JSON file.');

            return false;
        }

        if (! is_readable($path)) {
            $this->components->error('File exists but is not readable (permissions, or OneDrive/file lock).');

            return false;
        }

        $decoded = json_decode(File::get($path), true);
        if (! is_array($decoded)) {
            $this->components->error('File is not valid JSON.');

            return false;
        }

        foreach (['type', 'project_id', 'private_key', 'client_email'] as $field) {
            if (empty($decoded[$field])) {
                $this->components->warn("JSON is missing expected field: {$field}");
            }
        }

        if (($decoded['type'] ?? '') !== 'service_account') {
            $this->components->warn('"type" in JSON should be "service_account".');
        }

        $this->components->info('Service account JSON: OK');

        $fcm = app(FcmService::class);
        if ($fcm->isConfigured()) {
            $this->components->info('FCM (Admin SDK messaging): ready.');
        } else {
            $this->components->warn('FCM client did not initialize (check logs for PHP/kreait errors).');
        }

        return true;
    }

    private function checkWebPush(): void
    {
        $this->components->twoColumnDetail('Web push (Filament / browser token)', '');

        if (FirebaseWebPush::isConfigured()) {
            $this->components->info('All required FIREBASE_WEB_* variables and FIREBASE_WEB_VAPID_KEY are set.');

            return;
        }

        $this->components->warn('Web push is incomplete. In Firebase Console → Project settings:');
        $this->line('  • General → Your apps → Web app → copy into FIREBASE_WEB_*');
        $this->line('  • Cloud Messaging → Web Push certificates → Key pair → FIREBASE_WEB_VAPID_KEY');
        $this->newLine();
        $this->line('Missing in .env:');
        foreach (FirebaseWebPush::missingWebEnvVariables() as $name) {
            $this->line('  - '.$name);
        }
        $this->newLine();
        $this->line('After editing .env: php artisan config:clear');
    }
}
