<?php

namespace App\Services;

use App\Exceptions\SmsDispatchException;
use Illuminate\Support\Facades\Cache;
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

    protected $topupUrl;

    protected $topupUid;

    protected $topupUsername;

    protected $topupPassword;

    protected $topupDescription;

    protected $topupVoucherNumber;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->apiUsername = config('services.deywuro.api_username');
        $this->apiPassword = config('services.deywuro.api_password');
        $this->senderId = config('services.deywuro.sender_id');
        $this->apiUrl = config('services.deywuro.sms_url');
        $this->creditUrl = config('services.deywuro.balance_url');
        $this->topupUrl = config('services.deywuro.topup_url');
        $this->topupPassword = config('services.deywuro.topup_password');
        $this->topupUid = config('services.deywuro.topup_uid');
        $this->topupDescription = config('services.deywuro.topup_description');
        $this->topupVoucherNumber = config('services.deywuro.topup_voucher_number');
        $this->topupUsername = config('services.deywuro.topup_username');

    }

    private function fetchBalanceFromApi(): float
    {
        $balanceResponse = Http::get($this->creditUrl, [
            'username' => $this->apiUsername,
            'password' => $this->apiPassword,
        ]);

        if ($balanceResponse->failed()) {
            Log::channel('broadcast-msg')->warning('Failed to fetch SMS balance from API');

            return 0.0;
        }

        Log::channel('broadcast-msg')->info('Fetched SMS balance from API successfully. Response: '.json_encode($balanceResponse->json()));

        return round((float) $balanceResponse->json('balance', 0), 2);
    }

    public function getBalance(): float
    {
        return Cache::remember('sms_balance', now()->addMinutes(2), function () {
            return $this->fetchBalanceFromApi();
        });

    }

    public function getEstimatedCost(int $recipientCount, string $message): float
    {
        if ($recipientCount <= 0 || trim($message) === '') {
            return 0.0;
        }

        return round($recipientCount * ceil(strlen($message) / 160) * 0.03, 2);
    }

    public function topUpSms(string $phoneNumber, string $network, float $amount): void
    {

        $data = [
            'msisdn' => '233'.substr($phoneNumber, -9),
            'amount' => $amount,
            'description' => $this->topupDescription,
            'uid' => $this->topupUid,
            'uname' => $this->apiUsername,
            'user_id' => $this->topupUsername,
            'password' => $this->topupPassword,
            'username' => $this->apiUsername,
            'network' => $network,
            'voucher_number' => $this->topupVoucherNumber,
            'option' => 'SMS',
        ];

        $topUpResponse = Http::asJson()->post($this->topupUrl, $data);

        $logContext = [
            'url' => $this->topupUrl,
            'phone' => substr($phoneNumber, 0, 3).'****'.substr($phoneNumber, -3),
            'network' => $network,
            'amount' => $amount,
            'status' => $topUpResponse->status(),
            'body' => $topUpResponse->body(),
        ];

        Log::channel('broadcast-msg')->info('Initiated SMS top-up request.', $logContext);

        if ($topUpResponse->failed()) {
            Log::channel('broadcast-msg')->warning('SMS top-up request failed', $logContext);

            throw new SmsDispatchException('SMS top-up request failed.');
        }

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

        unset($data['password']);
        $desensitizedRecipients = collect($recipients)->map(fn ($number) => substr($number, 0, 3).'****'.substr($number, -3))->all();
        Log::channel('broadcast-msg')->info('Sent SMS chunk. Request data: '.json_encode(array_merge($data, ['destination' => $desensitizedRecipients])).'. Response: '.json_encode($response->json()));

        if ($response->failed()) {
            Log::channel('broadcast-msg')->warning('SMS dispatch failed for chunk. Request data: '.json_encode(array_merge($data, ['destination' => $desensitizedRecipients])).'. Response: '.json_encode($response->json()));

            throw new SmsDispatchException('Failed to send SMS chunk.');
        }

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
