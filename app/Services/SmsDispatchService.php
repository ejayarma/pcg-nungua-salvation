<?php

namespace App\Services;

use App\Exceptions\SmsDispatchException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        // Check balance from SMS service
        $balanceResponse = Http::get($this->creditUrl, [
            'username' => $this->apiUsername,
            'password' => $this->apiPassword,
        ]);

        if ($balanceResponse->failed()) {
            throw new SmsDispatchException('Failed to check SMS balance');
        }

        $balance = $balanceResponse->json('balance');
        $balance = round((float) $balance, 3);

        $estimatedCost = count($recipients) * (strlen($message) / 160) * 0.03;
        Log::channel('broadcast-msg')->info("Current SMS balance: {$balance}. Estimated cost for this broadcast: {$estimatedCost}.");

        if ($balance < $estimatedCost) {
            throw new SmsDispatchException('Insufficient SMS balance');
        }

        collect($recipients)->chunk(10)->each(function (array $chunk, $index) use ($message) {
            $smsResponse = $this->sendChunk($chunk, $message);
            Log::channel('broadcast-msg')->info('SMS sent to chunk: '.$index + 1 .'. Response: '.json_encode($smsResponse));
        });
    }

    private function sendChunk(array $recipients, string $message)
    {

        $data = [
            'username' => $this->apiUsername,
            'password' => $this->apiPassword,
            'source' => $this->senderId,
            'destination' => $this->formatRecipients($recipients),
            'message' => $message,
            'ol' => false,
        ];

        $response = Http::post($this->apiUrl, $data)->dump();

        return $response->json();
    }

    private function formatRecipients(array $recipients): string
    {
        return collect($recipients)->map(function ($number) {
            $formatted = preg_replace('/\D/', '', $number);

            return '233'.substr($formatted, -9);
        })->implode(',');
    }
}
