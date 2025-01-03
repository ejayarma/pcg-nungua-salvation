<?php

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
        Schema::create('member_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->string('zip_code')->nullable();
            $table->string('street')->nullable();
            $table->string('city');
            $table->string('digital_address');
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_addresses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('member_id');
            $table->dropConstrainedForeignId('state_id');
        });

        Schema::dropIfExists('member_addresses');
    }
};
