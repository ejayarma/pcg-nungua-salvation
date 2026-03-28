<?php

namespace App\Services;

use App\Mail\MessageBroadcastMail;
use Illuminate\Support\Facades\Mail;

class EmailDispatchService
{
    private const BATCH_SIZE = 50;

    private const DELAY_BETWEEN_BATCHES_SECONDS = 60;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function sendEmail(array $recipients, string $message): void
    {
        collect($recipients)
            ->chunk(self::BATCH_SIZE)
            ->each(function ($chunk, int $batchIndex) use ($message) {
                $delayInSeconds = $batchIndex * self::DELAY_BETWEEN_BATCHES_SECONDS;
                $this->dispatchBatch($chunk->all(), $message, $delayInSeconds);
            });
    }

    private function dispatchBatch(array $recipients, string $message, int $delaySeconds): void
    {
        dispatch(function () use ($recipients, $message) {
            $this->sendChunk($recipients, $message);
        })->delay(now()->addSeconds($delaySeconds));
    }

    private function sendChunk(array $recipients, $message): void
    {
        Mail::to($recipients)->send(new MessageBroadcastMail($message));
    }
}
