<?php

namespace App\Console\Commands;

use App\Models\GenerationalGroup;
use App\Models\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SeedMembersFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-members-from-csv
                            {--file=resources/data/salvation-database.csv : Path to the CSV file}
                            {--generational-group= : ID of the generational group to assign members to (optional, will use CSV value if not provided)}
                            {--default-generational-group=1 : Default generational group ID if not found in CSV}
                            {--skip-duplicates : Skip members with duplicate phone numbers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the members table with data from the salvation-database.csv file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path($this->option('file'));
        $generationalGroupId = $this->option('generational-group');
        $defaultGenerationalGroupId = $this->option('default-generational-group');
        $skipDuplicates = $this->option('skip-duplicates');

        // Check if file exists
        if (! file_exists($filePath)) {
            $this->error("CSV file not found: {$filePath}");

            return 1;
        }

        $this->info("Seeding members from CSV file: {$filePath}");

        // Read CSV file
        $file = fopen($filePath, 'r');
        $row = 0;
        $membersCreated = 0;
        $membersSkipped = 0;
        $membersUpdated = 0;
        $errors = [];

        // Skip header row
        fgetcsv($file);

        DB::beginTransaction();

        try {
            while (($data = fgetcsv($file)) !== false) {
                $row++;

                // Skip empty rows
                if (empty($data[0]) || trim($data[0]) === '') {
                    continue;
                }

                // Parse CSV columns
                $name = trim($data[0] ?? '');
                $gender = trim($data[1] ?? '');
                $phone = trim($data[2] ?? '');
                $dateOfBirth = trim($data[3] ?? '');
                $communionStatus = trim($data[7] ?? '');
                $occupation = trim($data[8] ?? '');
                $generationalGroup = trim($data[9] ?? '');
                $email = trim($data[11] ?? '');
                $address = trim($data[12] ?? '');

                // Validate required fields
                if (empty($name)) {
                    $errors[] = "Row {$row}: Name is required";
                    $membersSkipped++;

                    continue;
                }

                if (empty($phone)) {
                    $errors[] = "Row {$row}: Phone number is required for {$name}";
                    $membersSkipped++;

                    continue;
                }

                // Clean phone number (remove spaces, slashes, etc.)
                $phone = preg_replace('/[^0-9]/', '', $phone);

                // Validate phone number format
                if (! preg_match('/^[0-9]{9,15}$/', $phone)) {
                    $errors[] = "Row {$row}: Invalid phone number format for {$name}: {$phone}";
                    $membersSkipped++;

                    // continue;
                }

                // Check if member with this phone number already exists
                $existingMember = Member::where('phone', $phone)->first();
                if ($existingMember) {
                    if ($skipDuplicates) {
                        $this->warn("Row {$row}: Member with phone {$phone} already exists ({$existingMember->name}). Skipping.");
                        $membersSkipped++;

                        continue;
                    } else {
                        // Update existing member
                        $existingMember->update([
                            'name' => $name,
                            'gender' => $this->normalizeGender($gender),
                            'date_of_birth' => $this->parseDate($dateOfBirth),
                            'is_communicant' => $this->parseCommunionStatus($communionStatus),
                            'occupation' => $occupation !== '-' ? $occupation : null,
                            'email' => $this->parseEmail($email),
                        ]);
                        $this->warn("Row {$row}: Updated existing member: {$name} (Phone: {$phone})");
                        $membersUpdated++;

                        continue;
                    }
                }

                // Get generational group ID
                $groupId = $generationalGroupId ?? $this->getGenerationalGroupId($generationalGroup, $defaultGenerationalGroupId);

                // Create member
                Member::create([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $this->parseEmail($email),
                    'date_of_birth' => $this->parseDate($dateOfBirth),
                    'generational_group_id' => $groupId,
                    'gender' => $this->normalizeGender($gender),
                    'is_communicant' => $this->parseCommunionStatus($communionStatus),
                    'occupation' => $occupation !== '-' ? $occupation : null,
                ]);

                $membersCreated++;
            }

            DB::commit();

            $this->info('');
            $this->info('Seeding completed successfully!');
            $this->info("Members created: {$membersCreated}");
            $this->info("Members updated: {$membersUpdated}");
            $this->info("Members skipped: {$membersSkipped}");

            if (! empty($errors)) {
                $this->warn('');
                $this->warn('Errors encountered:');
                foreach ($errors as $error) {
                    $this->warn("  {$error}");
                }
            }

            Log::info('Members seeded from CSV', [
                'file' => $filePath,
                'created' => $membersCreated,
                'updated' => $membersUpdated,
                'skipped' => $membersSkipped,
                'errors' => $errors,
            ]);

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error seeding members: {$e->getMessage()}");
            Log::error('Error seeding members from CSV', [
                'error' => $e->getMessage(),
                'file' => $filePath,
            ]);

            return 1;
        } finally {
            fclose($file);
        }
    }

    /**
     * Normalize gender value
     */
    private function normalizeGender(string $gender): string
    {
        $gender = strtolower(trim($gender));

        if (in_array($gender, ['male', 'm'])) {
            return 'Male';
        } elseif (in_array($gender, ['female', 'f'])) {
            return 'Female';
        }

        return 'Male'; // Default
    }

    /**
     * Parse date from various formats
     */
    private function parseDate(string $date): ?string
    {
        if (empty($date) || $date === '-') {
            return null;
        }

        // Try different date formats
        $formats = ['d/m/Y', 'd/m/y', 'Y-m-d', 'd-m-Y', 'd-m-y'];

        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $date);
            if ($parsed !== false) {
                return $parsed->format('Y-m-d');
            }
        }

        // Try strtotime as fallback
        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    /**
     * Parse communion status to boolean
     */
    private function parseCommunionStatus(string $status): bool
    {
        $status = strtolower(trim($status));

        return in_array($status, ['communicant', 'yes', 'true', '1']);
    }

    /**
     * Parse email address
     */
    private function parseEmail(string $email): ?string
    {
        if (empty($email) || $email === '-' || $email === '') {
            return null;
        }

        // Basic email validation
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        return null;
    }

    /**
     * Get generational group ID from name
     */
    private function getGenerationalGroupId(string $groupName, int $defaultId): int
    {
        if (empty($groupName) || $groupName === '-') {
            return $defaultId;
        }

        // Map common group names
        $groupMap = [
            "men's fellowship" => 'Men',
            "women's fellowship" => 'Women',
            'yaf' => 'YAF',
            'ypg' => 'YPG',
            'child' => 'Child',
            'junior' => 'jy',
        ];

        $normalizedName = strtolower(trim($groupName));
        $mappedName = $groupMap[$normalizedName] ?? $groupName;

        // Try to find the group by name
        $group = GenerationalGroup::where('name', 'LIKE', "%{$mappedName}%")->first();

        if ($group) {
            return $group->id;
        }

        // Return default if not found
        return $defaultId;
    }
}
