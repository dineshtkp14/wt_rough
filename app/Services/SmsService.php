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
        $this->username = config('services.sms.username') ?: 'om_hari';
        $this->apiKey = config('services.sms.api_key') ?: 'DE932FD6F0E9C395DCED38SXV07IEDCAF4';
        $this->password = config('services.sms.password') ?: 'Nepal12345#';
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
            if (empty($this->username) || (empty($this->apiKey) && empty($this->password))) {
                return [
                    'success' => false,
                    'status' => null,
                    'error' => 'SMS credentials are missing. Check SMS_USERNAME, SMS_API_KEY, and SMS_PASSWORD in .env.',
                ];
            }

            // Use the account API key, with password authentication as fallback.
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

            $response = Http::asForm()
                ->timeout(20)
                ->retry(1, 500)
                ->post($this->apiUrl, $payload);

            if (!$response->successful() && $this->password) {
                Log::warning('SMS API-key authentication failed, trying password', [
                    'phone' => $phoneNumber,
                ]);

                $payload['password'] = $this->password;
                unset($payload['key']);

                $response = Http::asForm()
                    ->timeout(20)
                    ->retry(1, 500)
                    ->post($this->apiUrl, $payload);
            }

            $data = $response->json();
            $body = $response->body();

            Log::info('SMS sent', [
                'phone' => $phoneNumber,
                'status' => $response->status(),
                'response' => $data ?? $body,
            ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $data,
                'body' => $body,
            ];

        } catch (\Exception $e) {
            Log::error('SMS failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'status' => null,
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
            if (empty($this->username) || (empty($this->apiKey) && empty($this->password))) {
                return [
                    'success' => false,
                    'status' => null,
                    'error' => 'SMS credentials are missing. Check SMS_USERNAME, SMS_API_KEY, and SMS_PASSWORD in .env.',
                ];
            }

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

            $response = Http::asForm()
                ->timeout(20)
                ->retry(1, 500)
                ->post($this->apiUrl, $payload);

            if (!$response->successful() && $this->password) {
                $payload['password'] = $this->password;
                unset($payload['key']);

                $response = Http::asForm()
                    ->timeout(20)
                    ->retry(1, 500)
                    ->post($this->apiUrl, $payload);
            }

            $data = $response->json();
            $body = $response->body();

            Log::channel('sms')->info('Bulk SMS sent', [
                'count' => count($phoneNumbers),
                'status' => $response->status(),
                'response' => $data ?? $body,
            ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $data,
                'body' => $body,
            ];

        } catch (\Exception $e) {
            Log::channel('sms')->error('Bulk SMS failed', [
                'count' => count($phoneNumbers),
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'status' => null,
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
