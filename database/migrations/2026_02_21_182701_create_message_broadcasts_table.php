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
        Schema::create('message_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('medium'); // EMAIL, SMS
            $table->string('recipient_group'); // ALL, GENERATIONAL_GROUP, CUSTOM
            $table->json('recipients')->nullable(); // Store recipient IDs or filters as JSON
            $table->integer('recipient_count')->default(0);
            $table->string('status')->default('PENDING'); // PENDING, SENT, FAILED
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_broadcasts');
    }
};
