<?php

use App\Http\Controllers\FirebaseMessagingSwController;
use App\Http\Controllers\WebFcmTokenController;
use Illuminate\Support\Facades\Route;

Route::get('/firebase-messaging-sw.js', FirebaseMessagingSwController::class)
    ->name('firebase.messaging-sw');

Route::middleware('web')->group(function () {
    Route::get('/admin/set-locale/{locale}', function (string $locale) {
        abort_unless(in_array($locale, ['en', 'ar'], true), 404);
        session(['filament_locale' => $locale]);

        return redirect()->back();
    })->name('admin.set-locale');

    Route::middleware(['auth', 'throttle:60,1'])->post('/web/fcm-token', [WebFcmTokenController::class, 'store'])
        ->name('web.fcm-token.store');
});

Route::get('/', function () {
    return view('landing');
});

Route::view('/docs/api', 'api-docs')->name('docs.api');
