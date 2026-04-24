<?php

namespace App\Console\Commands;

use App\Mail\SmsBalanceLowMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckSmsBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-sms-balance {--threshold=25 : Minimum balance threshold}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SMS balance and notify admins if balance is low';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = (float) $this->option('threshold');
        $balance = $this->getSmsCreditBalance();

        $this->info("Current SMS balance: {$balance}");

        if (!($balance < $threshold)) {
            $this->warn("SMS balance ({$balance}) is below threshold ({$threshold})");
            $this->notifyAdmins($balance, $threshold);
        } else {
            $this->info('SMS balance is healthy');
        }
    }

    private function getSmsCreditBalance(): float
    {
        try {
            $creditUrl = config('services.deywuro.balance_url');
            $apiUsername = config('services.deywuro.api_username');
            $apiPassword = config('services.deywuro.api_password');

            $response = Http::get($creditUrl, [
                'username' => $apiUsername,
                'password' => $apiPassword,
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to fetch SMS balance');
            }

            $balance = $response->json('balance');

            return round((float) $balance, 3);
        } catch (\Exception $e) {
            Log::channel('broadcast-msg')->error('Error fetching SMS balance: '.$e->getMessage());
            throw $e;
        }
    }

    private function notifyAdmins(float $balance, float $threshold): void
    {
        try {
            $admins = User::where('is_admin', true)->get();

            if ($admins->isEmpty()) {
                Log::channel('broadcast-msg')->warning('No admins found to notify about low SMS balance');
                $this->warn('No admins found to notify');

                return;
            }

            foreach ($admins as $admin) {
                Mail::send(new SmsBalanceLowMail($admin, $balance, $threshold));
            }

            Log::channel('broadcast-msg')->info("SMS balance low notification sent to {$admins->count()} admin(s)");
            $this->info("Notification sent to {$admins->count()} admin(s)");
        } catch (\Exception $e) {
            Log::channel('broadcast-msg')->error('Error notifying admins about low SMS balance: '.$e->getMessage());
            $this->error('Failed to send notifications: '.$e->getMessage());
        }
    }
}
