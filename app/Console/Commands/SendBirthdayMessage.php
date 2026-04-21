<?php

namespace App\Console\Commands;

use App\Exceptions\SmsDispatchException;
use App\Mail\MembersBirthdayNoticeToAdminEmail;
use App\Models\Member;
use App\Models\User;
use App\Services\SmsDispatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBirthdayMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-birthday-message
                            {--message= : Optional custom birthday message}
                            {--dry-run : Show recipients without sending SMS}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send birthday SMS messages to members whose birthday is today.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $message = $this->option('message') ?: 'Wishing you a blessed day from PCG Salvation Congregation, Nungua!';
        $dryRun = $this->option('dry-run');

        $members = Member::query()
            ->with('contactPerson')
            ->whereNotNull('date_of_birth')
            ->whereRaw('EXTRACT(MONTH FROM date_of_birth) = ? AND EXTRACT(DAY FROM date_of_birth) = ?', [now()->month, now()->day])
            ->get();

        if ($members->isEmpty()) {
            $this->info('No members have birthdays today.');

            return 0;
        }

        // Filter members with valid phone numbers
        $membersWithPhones = $members
            ->map(fn ($member) => [
                'member' => $member,
                'phone' => $member->phone ?: ($member->contactPerson?->phone ?? null),
            ])
            ->filter(fn ($item) => $item['phone'])
            ->values();

        if ($membersWithPhones->isEmpty()) {
            $this->warn('No valid phone numbers found for today\'s birthday members.');

            return 0;
        }

        $this->info('Found '.$members->count().' birthday member(s) today.');
        $this->info('Sending personalized SMS to '.$membersWithPhones->count().' member(s).');

        $adminEmails = User::where('is_admin', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter()
            ->unique()
            ->values();

        if ($dryRun) {
            $this->info('Dry run enabled. The following admin emails would receive the birthday notice:');
            $adminEmails->each(fn ($email) => $this->line($email));
            $this->info('Dry run enabled. The following personalized messages would be sent:');
            $membersWithPhones->each(function ($item) use ($message) {
                $memberName = $item['member']->name;
                $personalizedMessage = "Happy birthday, {$memberName}! {$message}";
                $this->line("{$item['phone']}: {$personalizedMessage}");
            });

            return 0;
        }

        if ($adminEmails->isNotEmpty()) {
            Mail::to($adminEmails->all())
                ->send(new MembersBirthdayNoticeToAdminEmail($members, $message));

            $this->info('Birthday notice email sent to '.$adminEmails->count().' admin user(s).');
        } else {
            $this->warn('No admin users with email addresses found to notify.');
        }

        try {
            $smsService = app(SmsDispatchService::class);
            $failedNumbers = [];

            foreach ($membersWithPhones as $item) {
                $memberName = $item['member']->name;
                $personalizedMessage = "Happy birthday, {$memberName}! {$message}";

                try {
                    $smsService->sendSms([$item['phone']], $personalizedMessage);
                } catch (SmsDispatchException $e) {
                    $failedNumbers[] = $item['phone'];
                    Log::channel('broadcast-msg')->warning('Failed to send SMS to '.$item['phone'], [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($failedNumbers === []) {
                $this->info('Personalized birthday SMS sent to all '.$membersWithPhones->count().' member(s) successfully.');

                return 0;
            } else {
                $this->warn('Sent SMS to '.$membersWithPhones->count() - count($failedNumbers).' member(s). Failed to send to '.count($failedNumbers).' number(s).');

                return 1;
            }
        } catch (\Exception $exception) {
            $this->error('An unexpected error occurred while sending birthday SMS broadcast: '.$exception->getMessage());
            Log::channel('broadcast-msg')->error('Birthday SMS broadcast unexpected failure.', [
                'error' => $exception->getMessage(),
            ]);

            return 1;
        }
    }
}
