<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private $apiUrl = 'https://spellcpaas.com/api/smsapi';
    private $username;
    private $apiKey;
    private $password;
    private $campaign;
    private $routeId;

    public function __construct()
    {
        $this->username = config('services.sms.username');
        $this->apiKey = config('services.sms.api_key');
        $this->password = config('services.sms.password');
        $this->campaign = config('services.sms.campaign', 'Default');
        $this->routeId = config('services.sms.route_id', 'SI_Alert');
    }

    /**
     * Send SMS to a single contact
     * 
     * @param string $phoneNumber
     * @param string $message
     * @param string $scheduledTime (optional) Format: YYYY-MM-DD HH:MM
     * @return array
     */
    public function send($phoneNumber, $message, $scheduledTime = null)
    {
        try {
            // Try with API Key first (recommended method)
            $payload = [
                'username' => $this->username,
                'key' => $this->apiKey,
                'campaign' => $this->campaign,
                'routeid' => $this->routeId,
                'type' => 'text',
                'contacts' => $phoneNumber,
                'msg' => $message,
                'responsetype' => 'json'
            ];

            if ($scheduledTime) {
                $payload['time'] = $scheduledTime;
            }

            $response = Http::post($this->apiUrl, $payload);

            // If INVALID API KEY error, try with username + password
            if ($response->status() === 400 && strpos($response->body(), 'INVALID API KEY') !== false) {
                Log::channel('sms')->warning('API Key method failed, trying username/password method', [
                    'phone' => $phoneNumber,
                ]);

                // Alternative: use username + password instead
                $payload['password'] = $this->password;
                unset($payload['key']);

                $response = Http::post($this->apiUrl, $payload);
            }

            Log::channel('sms')->info('SMS sent', [
                'phone' => $phoneNumber,
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::channel('sms')->error('SMS failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS to multiple contacts
     * 
     * @param array $phoneNumbers
     * @param string $message
     * @param string $scheduledTime (optional)
     * @return array
     */
    public function sendBulk(array $phoneNumbers, $message, $scheduledTime = null)
    {
        try {
            $contacts = implode(',', $phoneNumbers);

            $payload = [
                'username' => $this->username,
                'key' => $this->apiKey,
                'campaign' => $this->campaign,
                'routeid' => $this->routeId,
                'type' => 'text',
                'contacts' => $contacts,
                'msg' => $message,
                'responsetype' => 'json'
            ];

            if ($scheduledTime) {
                $payload['time'] = $scheduledTime;
            }

            $response = Http::post($this->apiUrl, $payload);

            Log::channel('sms')->info('Bulk SMS sent', [
                'count' => count($phoneNumbers),
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::channel('sms')->error('Bulk SMS failed', [
                'count' => count($phoneNumbers),
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to international format
     * 
     * @param string $phone
     * @return string
     */
    public static function formatPhoneNumber($phone)
    {
        // Remove all non-digit characters
        $phone = preg_replace('/\D+/', '', $phone);

        // If it's a 10-digit number, assume it's Nepal (977 country code)
        if (strlen($phone) === 10) {
            $phone = '977' . $phone;
        }

        return $phone;
    }
}
