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
        $message = $this->option('message') ?: 'Happy birthday! Wishing you a blessed day from PCG Nungua Salvation.';
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

        $phoneNumbers = $members
            ->map(fn ($member) => $member->phone ?: ($member->contactPerson?->phone ?? null))
            ->filter()
            ->unique()
            ->values();

        if ($phoneNumbers->isEmpty()) {
            $this->warn('No valid phone numbers found for today\'s birthday members.');

            return 0;
        }

        $this->info('Found '.$members->count().' birthday member(s) today.');
        $this->info('Sending SMS to '.$phoneNumbers->count().' unique phone number(s).');

        $adminEmails = User::where('is_admin', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter()
            ->unique()
            ->values();

        if ($dryRun) {
            $this->info('Dry run enabled. The following admin emails would receive the birthday notice:');
            $adminEmails->each(fn ($email) => $this->line($email));
            $this->info('Dry run enabled. The following numbers would receive the message:');
            $phoneNumbers->each(fn ($phone) => $this->line($phone));

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
            app(SmsDispatchService::class)->sendSms($phoneNumbers->toArray(), $message);
            $this->info('Birthday SMS broadcast completed successfully.');

            return 0;
        } catch (SmsDispatchException $exception) {
            $this->error('Failed to send birthday SMS broadcast: '.$exception->getMessage());
            Log::channel('broadcast-msg')->error('Birthday SMS broadcast failed.', [
                'error' => $exception->getMessage(),
                'numbers' => $phoneNumbers->all(),
            ]);

            return 1;
        } catch (\Exception $exception) {
            $this->error('An unexpected error occurred while sending birthday SMS broadcast: '.$exception->getMessage());
            Log::channel('broadcast-msg')->error('Birthday SMS broadcast unexpected failure.', [
                'error' => $exception->getMessage(),
                'numbers' => $phoneNumbers->all(),
            ]);

            return 1;
        }
    }
}
