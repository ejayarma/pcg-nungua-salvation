<?php

namespace App\Services;

use App\Exceptions\SmsDispatchException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsDispatchService
{
    private const BATCH_SIZE = 20;

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
        $recipients = $this->normalizeRecipients($recipients);

        if (empty($recipients)) {
            Log::channel('broadcast-msg')->warning('No valid SMS recipients found after normalization.');

            return;
        }

        Log::channel('broadcast-msg')->info('Normalized SMS recipients count: '.count($recipients).'. Recipients: '.json_encode($recipients));

        // Check balance from SMS service
        $balanceResponse = Http::get($this->creditUrl, [
            'username' => $this->apiUsername,
            'password' => $this->apiPassword,
        ]);

        if ($balanceResponse->failed()) {
            throw new SmsDispatchException('Failed to check SMS balance');
        }

        Log::channel('broadcast-msg')->info('Checked SMS balance. Response: '.json_encode($balanceResponse->json()));

        $balance = $balanceResponse->json('balance');
        $balance = round((float) $balance, 3);

        $estimatedCost = count($recipients) * ceil(strlen($message) / 160) * 0.03;
        Log::channel('broadcast-msg')->info("Current SMS balance: {$balance}. Estimated cost for this broadcast: {$estimatedCost}.");

        if ($balance < $estimatedCost) {
            throw new SmsDispatchException('Insufficient SMS balance');
        }

        collect($recipients)->chunk(self::BATCH_SIZE)->each(function ($chunk, $index) use ($message) {
            $smsResponse = $this->sendChunk($chunk->toArray(), $message);
            Log::channel('broadcast-msg')->info('SMS sent to chunk: '.($index + 1).'. Response: '.json_encode($smsResponse));
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

        $response = Http::post($this->apiUrl, $data);

        Log::channel('broadcast-msg')->info('Sent SMS chunk. Request data: '.json_encode($data).'. Response: '.json_encode($response->json()));

        return $response->json();
    }

    private function formatRecipients(array $recipients): string
    {
        return collect($recipients)->implode(',');
    }

    private function normalizeRecipients(array $recipients): array
    {
        return collect($recipients)
            ->map(fn ($number) => preg_replace('/\D+/', '', $number))
            ->filter()
            ->map(fn ($formatted) => substr($formatted, -9))
            ->filter()
            ->map(fn ($number) => '233'.$number)
            ->unique()
            ->values()
            ->all();
    }
}
