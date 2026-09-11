<?php

namespace App\Support;

final class FirebaseWebPush
{
    /**
     * @var list<string>
     */
    private const REQUIRED_KEYS = [
        'api_key',
        'auth_domain',
        'project_id',
        'storage_bucket',
        'messaging_sender_id',
        'app_id',
        'vapid_key',
    ];

    /**
     * @var array<string, string>
     */
    private const WEB_KEY_TO_ENV = [
        'api_key' => 'FIREBASE_WEB_API_KEY',
        'auth_domain' => 'FIREBASE_WEB_AUTH_DOMAIN',
        'project_id' => 'FIREBASE_WEB_PROJECT_ID',
        'storage_bucket' => 'FIREBASE_WEB_STORAGE_BUCKET',
        'messaging_sender_id' => 'FIREBASE_WEB_MESSAGING_SENDER_ID',
        'app_id' => 'FIREBASE_WEB_APP_ID',
        'vapid_key' => 'FIREBASE_WEB_VAPID_KEY',
    ];

    public static function isConfigured(): bool
    {
        $web = config('firebase.web', []);

        foreach (self::REQUIRED_KEYS as $key) {
            $value = $web[$key] ?? null;
            if (! is_string($value) || trim($value) === '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @return list<string> Missing .env variable names (FIREBASE_WEB_*)
     */
    public static function missingWebEnvVariables(): array
    {
        $web = config('firebase.web', []);
        $missing = [];

        foreach (self::REQUIRED_KEYS as $key) {
            $value = $web[$key] ?? null;
            if (! is_string($value) || trim($value) === '') {
                $missing[] = self::WEB_KEY_TO_ENV[$key];
            }
        }

        return $missing;
    }

    /**
     * @return array<string, string|null>
     */
    public static function firebaseJsConfig(): array
    {
        $web = config('firebase.web', []);

        $config = [
            'apiKey' => (string) ($web['api_key'] ?? ''),
            'authDomain' => (string) ($web['auth_domain'] ?? ''),
            'projectId' => (string) ($web['project_id'] ?? ''),
            'storageBucket' => (string) ($web['storage_bucket'] ?? ''),
            'messagingSenderId' => (string) ($web['messaging_sender_id'] ?? ''),
            'appId' => (string) ($web['app_id'] ?? ''),
        ];

        if (is_string($web['measurement_id'] ?? null) && trim((string) $web['measurement_id']) !== '') {
            $config['measurementId'] = trim((string) $web['measurement_id']);
        }

        return $config;
    }

    public static function vapidKey(): string
    {
        return trim((string) (config('firebase.web.vapid_key') ?? ''));
    }
}
