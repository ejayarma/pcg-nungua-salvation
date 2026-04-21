<?php

use App\Models\Member;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('member_addresses', function (Blueprint $table) {
            $table->string('city')->nullable()->change();
            $table->string('digital_address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Member::withTrashed()->chunkById(100, function ($members) {
            foreach ($members as $member) {
                if ($member->address) {
                    if (is_null($member->address->city)) {
                        $member->address->city = 'Unknown';
                    }

                    if (is_null($member->address->digital_address)) {
                        $member->address->digital_address = 'Unknown';
                    }

                    $member->address->save();
                }
            }
        });

        Schema::table('member_addresses', function (Blueprint $table) {
            $table->string('city')->nullable(false)->change();
            $table->string('digital_address')->nullable(false)->change();
        });
    }
};
