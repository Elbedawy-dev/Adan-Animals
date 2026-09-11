<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Throwable;

class FcmService
{
    private ?Messaging $messaging = null;

    public function __construct()
    {
        $path = config('firebase.credentials');
        if (is_string($path) && $path !== '' && is_readable($path)) {
            $this->messaging = (new Factory)->withServiceAccount($path)->createMessaging();
        }
    }

    public function isConfigured(): bool
    {
        return $this->messaging !== null;
    }

    /**
     * @param  array<string, string>  $data
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): void
    {
        if (! $this->messaging) {
            return;
        }

        $payload = [];
        foreach ($data as $key => $value) {
            $payload[(string) $key] = is_scalar($value) ? (string) $value : json_encode($value);
        }

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body));

        if ($payload !== []) {
            $message = $message->withData($payload);
        }

        try {
            $this->messaging->send($message);
        } catch (MessagingException $e) {
            $this->handleSendFailure($e, $token);
        } catch (Throwable $e) {
            Log::warning('FCM send failed', ['error' => $e->getMessage()]);
        }
    }

    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $token = $user->fcm_token;
        if (! is_string($token) || $token === '') {
            return;
        }

        $this->sendToToken($token, $title, $body, $data);
    }

    private function handleSendFailure(MessagingException $e, string $token): void
    {
        $message = $e->getMessage();
        Log::warning('FCM messaging error', ['error' => $message]);

        if (
            str_contains($message, 'not found')
            || str_contains($message, 'NotFound')
            || str_contains($message, 'registration-token-not-registered')
        ) {
            User::where('fcm_token', $token)->update(['fcm_token' => null]);
        }
    }
}
