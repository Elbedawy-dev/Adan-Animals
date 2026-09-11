<?php

namespace App\Http\Controllers;

use App\Support\FirebaseWebPush;
use Illuminate\Http\Response;

class FirebaseMessagingSwController extends Controller
{
    public function __invoke(): Response
    {
        abort_unless(FirebaseWebPush::isConfigured(), 404);

        $js = view('firebase.messaging-sw', [
            'firebaseConfig' => FirebaseWebPush::firebaseJsConfig(),
        ])->render();

        return response($js, 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
