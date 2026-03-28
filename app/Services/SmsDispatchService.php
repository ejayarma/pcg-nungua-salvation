<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsDispatchService
{
    protected $apiUsername;

    protected $apiPassword;

    protected $senderId;

    protected $apiUrl;

    protected $creditUrl;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->apiUsername = config('services.deywuro.api_username');
        $this->apiPassword = config('services.deywuro.api_password');
        $this->senderId = config('services.deywuro.sender_id');
        $this->apiUrl = config('services.deywuro.sms_url');
        $this->creditUrl = config('services.deywuro.credit_url');
    }

    public function sendSms(array $recipients, string $message)
    {
        collect($recipients)->chunk(50)->each(function ($chunk) use ($message) {
            $this->sendChunk($chunk, $message);
        });
    }

    private function sendChunk($recipients, $message)
    {

        $data = [
            'username' => $this->apiUsername,
            'password' => $this->apiPassword,
            'source' => $this->senderId,
            'destination' => $this->formatRecipients($recipients),
            'message' => $message,
            'ol' => false,
        ];

        $response = Http::post($this->apiUrl, $data);

        return $response->json();
    }

    private function formatRecipients($recipients): string
    {
        return collect($recipients)->map(function ($number) {
            $formatted = preg_replace('/\D/', '', $number);

            return '233'.substr($formatted, -9);
        })->implode(',');
    }
}
