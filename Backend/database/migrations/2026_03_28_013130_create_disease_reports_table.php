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
        Schema::create('disease_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('The user who submitted this report');
            $table->foreignId('animal_id')
                ->constrained('animals')
                ->cascadeOnDelete()
                ->comment('The type of animal affected');
            $table->foreignId('region_id')
                ->nullable()
                ->constrained('regions')
                ->nullOnDelete()
                ->comment('Geographic region where the disease was spotted');
            $table->string('title');
            $table->text('description');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('severity', ['low', 'moderate', 'high'])->default('low');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')
                ->comment('pending = awaiting vet review, approved = confirmed & alerts sent, rejected = not confirmed');
            $table->text('rejection_reason')->nullable()
                ->comment('Filled by doctor when rejecting a report');
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('The doctor who reviewed this report');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_reports');
    }
};
