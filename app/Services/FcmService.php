<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected string $serverKey;
    protected string $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key', '');
    }

    public function sendToToken(string $fcmToken, array $notification, array $data = []): bool
    {
        if (empty($this->serverKey)) {
            logger()->error('FCM Server Key not configured');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, [
                'to' => $fcmToken,
                'notification' => $notification,
                'data' => $data,
                'priority' => 'high',
            ]);

            if ($response->successful()) {
                logger()->info('FCM notification sent successfully', [
                    'token' => substr($fcmToken, 0, 20) . '...',
                ]);
                return true;
            }

            logger()->error('FCM send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            logger()->error('FCM send exception: ' . $e->getMessage());
            return false;
        }
    }

    public function sendToMultipleTokens(array $fcmTokens, array $notification, array $data = []): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'details' => [],
        ];
        
        foreach ($fcmTokens as $token) {
            $sent = $this->sendToToken($token, $notification, $data);
            $results['details'][$token] = $sent;
            
            if ($sent) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }
        
        return $results;
    }

    public function sendToTopic(string $topic, array $notification, array $data = []): bool
    {
        if (empty($this->serverKey)) {
            logger()->error('FCM Server Key not configured');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, [
                'to' => '/topics/' . $topic,
                'notification' => $notification,
                'data' => $data,
                'priority' => 'high',
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            logger()->error('FCM topic send failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendDataOnly(string $fcmToken, array $data): bool
    {
        if (empty($this->serverKey)) {
            logger()->error('FCM Server Key not configured');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, [
                'to' => $fcmToken,
                'data' => $data,
                'priority' => 'high',
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            logger()->error('FCM data send failed: ' . $e->getMessage());
            return false;
        }
    }
}
