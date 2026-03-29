<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:clear
                            {--force : Force deletion without confirmation}
                            {--soft : Use soft deletes (default)}
                            {--hard : Permanently delete records including soft deleted ones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all members from the database (with related records)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $hardDelete = $this->option('hard');
        $softDelete = ! $hardDelete;

        // Count members
        $memberCount = Member::count();
        $trashedCount = Member::onlyTrashed()->count();

        if ($memberCount === 0 && $trashedCount === 0) {
            $this->info('No members found in the database.');

            return 0;
        }

        $this->info("Found {$memberCount} active member(s) in the database.");
        if ($trashedCount > 0) {
            $this->info("Found {$trashedCount} soft-deleted member(s) in the database.");
        }

        // Confirm deletion unless force option is used
        if (! $force) {
            if ($hardDelete) {
                $message = "Are you sure you want to PERMANENTLY delete all {$memberCount} member(s) and their related records?";
                if ($trashedCount > 0) {
                    $message = "Are you sure you want to PERMANENTLY delete all {$memberCount} active member(s) and {$trashedCount} soft-deleted member(s) and their related records?";
                }
            } else {
                $message = "Are you sure you want to soft delete all {$memberCount} member(s) and their related records?";
            }

            if (! $this->confirm($message)) {
                $this->info('Operation cancelled.');

                return 0;
            }
        }

        DB::beginTransaction();

        try {
            if ($hardDelete) {
                // Hard delete - permanently remove all records
                $this->info('Permanently deleting members and related records...');

                // Delete related records first (addresses and contact people)
                $deletedAddresses = DB::table('member_addresses')->whereIn('member_id', function ($query) {
                    $query->select('id')->from('members');
                })->delete();

                $deletedContacts = DB::table('contact_people')->whereIn('member_id', function ($query) {
                    $query->select('id')->from('members');
                })->delete();

                // Delete all members (including soft deleted ones)
                $deletedMembers = Member::withTrashed()->forceDelete();

                $this->info('Permanently deleted:');
                $this->info("  - {$deletedMembers} member(s)");
                $this->info("  - {$deletedAddresses} member address(es)");
                $this->info("  - {$deletedContacts} contact person(s)");

                Log::info('Members hard deleted', [
                    'members' => $deletedMembers,
                    'addresses' => $deletedAddresses,
                    'contacts' => $deletedContacts,
                ]);

            } else {
                // Soft delete - only mark as deleted
                $this->info('Soft deleting members and related records...');

                // Soft delete related records first
                $deletedAddresses = DB::table('member_addresses')
                    ->whereIn('member_id', function ($query) {
                        $query->select('id')->from('members')->whereNull('deleted_at');
                    })
                    ->update(['deleted_at' => now()]);

                $deletedContacts = DB::table('contact_people')
                    ->whereIn('member_id', function ($query) {
                        $query->select('id')->from('members')->whereNull('deleted_at');
                    })
                    ->update(['deleted_at' => now()]);

                // Soft delete all active members
                $deletedMembers = Member::whereNull('deleted_at')->update(['deleted_at' => now()]);

                $this->info('Soft deleted:');
                $this->info("  - {$deletedMembers} member(s)");
                $this->info("  - {$deletedAddresses} member address(es)");
                $this->info("  - {$deletedContacts} contact person(s)");

                Log::info('Members soft deleted', [
                    'members' => $deletedMembers,
                    'addresses' => $deletedAddresses,
                    'contacts' => $deletedContacts,
                ]);
            }

            DB::commit();

            $this->info('');
            $this->info('Operation completed successfully!');

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error clearing members: {$e->getMessage()}");
            Log::error('Error clearing members', [
                'error' => $e->getMessage(),
            ]);

            return 1;
        }
    }
}
