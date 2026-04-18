<?php

namespace App\Console\Commands;

use App\Enums\MessageBroadcastMedium;
use App\Enums\MessageBroadcastRecipientEnum;
use App\Enums\MessageBroadcastStatusEnum;
use App\Models\Member;
use App\Models\MessageBroadcast;
use App\Services\EmailDispatchService;
use App\Services\SmsDispatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class ProcessMessageBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-message-broadcast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send pending message broadcasts to their intended recipients via the specified medium (email or SMS)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel('broadcast-msg')->info('Starting message broadcast processing...');

        // Fetch pending message broadcasts from the database
        $pendingBroadcasts = \App\Models\MessageBroadcast::where('status', MessageBroadcastStatusEnum::PENDING)->get();

        $count = $pendingBroadcasts->count();

        $this->info("Found {$count} pending message broadcast(s) to process.");
        Log::channel('broadcast-msg')->info("Found {$count} pending message broadcast(s) to process.");

        foreach ($pendingBroadcasts as $broadcast) {
            try {
                // skip if scheduled time is in the future
                if ($broadcast->scheduled_at && Date::make($broadcast->scheduled_at)->isFuture()) {
                    continue;
                }

                if ($broadcast->medium === MessageBroadcastMedium::EMAIL->value) {
                    $this->sendEmailBroadcast($broadcast);
                } elseif ($broadcast->medium === MessageBroadcastMedium::SMS->value) {
                    $this->sendSmsBroadcast($broadcast);
                }

                // Update broadcast status to 'sent' after successful dispatch
                $broadcast->update(['status' => MessageBroadcastStatusEnum::SENT]);

            } catch (\Exception $e) {
                // Log any errors that occur during processing
                Log::channel('broadcast-msg')->error("Failed to process message broadcast ID {$broadcast->id}: ".$e->getMessage());

                $broadcast->update(['status' => MessageBroadcastStatusEnum::FAILED]);

            }
        }

        $this->info('Message broadcast processing completed.');
        Log::channel('broadcast-msg')->info('Message broadcast processing completed.');
    }

    /**
     * Send email broadcasts to recipients.
     *
     * @return void
     */
    private function sendEmailBroadcast(MessageBroadcast $broadcast)
    {
        // Logic to send email broadcasts
        Log::channel('broadcast-msg')->info('Sending email broadcast...');

        $members = $this->getMembersForBroadcast($broadcast);
        Log::channel('broadcast-msg')->info("Processing email broadcast ID {$broadcast->id} for {$members->count()} recipient(s).");

        $emailAddresses = collect($members)
            ->filter(fn ($member) => ! empty($member->email) || ($member->contactPerson && ! empty($member->contactPerson->email)))
            ->map(fn ($member) => $member->email ?? $member->contactPerson->email)
            ->values();

        Log::channel('broadcast-msg')->info('Extracted '.count($emailAddresses)." email address(es) for email broadcast ID {$broadcast->id}.", [$emailAddresses]);

        // Logic to send email to $emailAddresses using your preferred email service
        $emailDispatchService = app(EmailDispatchService::class);
        $emailDispatchService->sendEmail($emailAddresses->toArray(), $broadcast);

    }

    /**
     * Send SMS broadcasts to recipients.
     *
     * @return void
     */
    private function sendSmsBroadcast(MessageBroadcast $broadcast)
    {
        $members = $this->getMembersForBroadcast($broadcast);

        Log::channel('broadcast-msg')->info("Processing SMS broadcast ID {$broadcast->id} for {$members->count()} recipient(s).");

        $phoneNumbers = collect($members)
            ->filter(fn ($member) => ! empty($member->phone) || ($member->contactPerson && ! empty($member->contactPerson->phone)))
            ->map(fn ($member) => $member->phone ?? $member->contactPerson->phone)
            ->values();

        if ($broadcast->recipient_group === MessageBroadcastRecipientEnum::ALL) {
            $csvPhoneNumbers = $this->loadSalvationNumbers();
            Log::channel('broadcast-msg')->info('Loaded '.count($csvPhoneNumbers)." additional phone number(s) from salvation.csv for SMS broadcast ID {$broadcast->id}.", [$csvPhoneNumbers]);
            $phoneNumbers = $phoneNumbers->merge($csvPhoneNumbers);
        }

        Log::channel('broadcast-msg')->info('Extracted '.count($phoneNumbers)." phone number(s) for SMS broadcast ID {$broadcast->id}.", [$phoneNumbers]);

        // Logic to send SMS to $phoneNumbers using your preferred SMS gateway
        $smsDispatchService = app(SmsDispatchService::class);
        $smsDispatchService->sendSms($phoneNumbers->toArray(), $broadcast->message);
    }

    private function loadSalvationNumbers()
    {
        $csvPath = resource_path('data/salvation.csv');

        if (! file_exists($csvPath)) {
            Log::channel('broadcast-msg')->warning("Salvation CSV not found at {$csvPath}.");

            return collect();
        }

        return collect(file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
            ->map(fn ($line) => trim(preg_replace('/\D+/', '', $line)))
            ->filter()
            ->values();
    }

    /** Get members
     *
     */
    private function getMembersForBroadcast(MessageBroadcast $broadcast)
    {
        return match ($broadcast->recipient_group) {
            MessageBroadcastRecipientEnum::ALL => Member::query()->with('contactPerson')->get(),
            MessageBroadcastRecipientEnum::GENERATIONAL_GROUP => Member::query()->with('contactPerson')->whereIn('generational_group_id', $broadcast->recipients)->get(),
            MessageBroadcastRecipientEnum::CUSTOM => Member::query()->with('contactPerson')->whereIn('id', $broadcast->recipients)->get(),
        };
    }
}
