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
        Schema::create('vaccine_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_animal_id')
                ->constrained('user_animals')
                ->cascadeOnDelete();
            $table->foreignId('vaccine_id')
                ->constrained('vaccines')
                ->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->timestamp('taken_at')->nullable()
                ->comment('When the vaccine was actually administered');
            $table->enum('status', ['pending', 'done', 'missed'])->default('pending');
            $table->timestamp('notified_at')->nullable()
                ->comment('Last time a reminder notification was sent for this schedule');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_schedules');
    }
};
