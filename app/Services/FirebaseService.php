<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $projectId;

    protected $credentialsPath;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id') ?? env('FIREBASE_PROJECT_ID');
        $this->credentialsPath = storage_path('app/firebase-auth.json');
    }

    /**
     * Get OAuth 2.0 access token using Service Account
     */
    protected function getAccessToken()
    {
        if (! file_exists($this->credentialsPath)) {
            Log::error('Firebase Service Account JSON not found at: '.$this->credentialsPath);

            return null;
        }

        try {
            $scopes = ['https://www.googleapis.com/auth/cloud-platform'];
            $credentials = new ServiceAccountCredentials($scopes, $this->credentialsPath);
            $token = $credentials->fetchAuthToken();

            return $token['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Failed to generate Firebase Access Token: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Send notification via FCM v1 API
     */
    public function send($token, $title, $body, $data = [])
    {
        $accessToken = $this->getAccessToken();
        if (! $accessToken || ! $this->projectId || ! $token) {
            return false;
        }

        $endpoint = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_map('strval', $data), // V1 data values must be strings
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                    ],
                ],
                'webpush' => [
                    'headers' => [
                        'Urgency' => 'high',
                    ],
                    'notification' => [
                        'icon' => '/logo.png',
                    ],
                ],
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);

        if (! $response->successful()) {
            Log::error('FCM v1 Send Failed: '.$response->body());
        }

        return $response->successful();
    }

    /**
     * Notify all members of a group except a specific user
     */
    public function notifyGroup($groupId, $title, $body, $data = [], $excludeUserId = null)
    {
        $group = Group::with('users')->find($groupId);

        if (! $group) {
            return false;
        }

        $tokens = $group->users
            ->filter(function ($user) use ($excludeUserId) {
                return $user->id != $excludeUserId && ! empty($user->fcm_token);
            })
            ->pluck('fcm_token')
            ->toArray();

        // Note: v1 API doesn't support multicast with a single request.
        // We must loop through each token.
        foreach ($tokens as $token) {
            $this->send($token, $title, $body, $data);
        }

        return true;
    }
}
