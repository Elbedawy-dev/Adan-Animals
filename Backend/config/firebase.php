<?php

/**
 * Firebase configuration.
 *
 * Admin SDK (server-side FCM):
 *   FIREBASE_CREDENTIALS — path to the service-account JSON (relative to project root).
 *   Never commit that file; list it in .gitignore.
 *
 * Web push (Filament / browser):
 *   FIREBASE_WEB_* — client-side SDK keys (safe to expose).
 *   FIREBASE_WEB_VAPID_KEY — from Firebase Console → Project settings →
 *   Cloud Messaging → Web push certificates.
 *
 * The `projects` block is the format expected by kreait/laravel-firebase and
 * laravel-notification-channels/fcm.  The flat `credentials` key is kept for
 * our own FcmService / FirebaseWebPush helpers.
 */

$credentialsPath = ($path = env('FIREBASE_CREDENTIALS'))
    ? (str_starts_with($path, DIRECTORY_SEPARATOR) || preg_match('#^[A-Za-z]:[\\\\/]#', $path) === 1
        ? $path
        : base_path($path))
    : null;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Firebase project (kreait/laravel-firebase)
    |--------------------------------------------------------------------------
    */
    'default' => env('FIREBASE_PROJECT', 'app'),

    /*
    |--------------------------------------------------------------------------
    | Firebase project configurations (kreait/laravel-firebase)
    |--------------------------------------------------------------------------
    */
    'projects' => [
        'app' => [
            'credentials' => $credentialsPath,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Flat credential path (used by our FcmService + FirebaseWebPush)
    |--------------------------------------------------------------------------
    */
    'credentials' => $credentialsPath,

    /*
    |--------------------------------------------------------------------------
    | Web / client-side SDK keys
    |--------------------------------------------------------------------------
    */
    'web' => [
        'api_key'              => env('FIREBASE_WEB_API_KEY'),
        'auth_domain'          => env('FIREBASE_WEB_AUTH_DOMAIN'),
        'project_id'           => env('FIREBASE_WEB_PROJECT_ID'),
        'storage_bucket'       => env('FIREBASE_WEB_STORAGE_BUCKET'),
        'messaging_sender_id'  => env('FIREBASE_WEB_MESSAGING_SENDER_ID'),
        'app_id'               => env('FIREBASE_WEB_APP_ID'),
        'measurement_id'       => env('FIREBASE_WEB_MEASUREMENT_ID'),
        'vapid_key'            => env('FIREBASE_WEB_VAPID_KEY'),
    ],
];
