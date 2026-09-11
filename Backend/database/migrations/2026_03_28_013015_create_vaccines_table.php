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
        Schema::create('vaccines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')
                ->constrained('animals')
                ->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('doses_count')->default(1)
                ->comment('Total number of doses required');
            $table->unsignedInteger('interval_days')->nullable()
                ->comment('Days between doses or annual repeat interval. NULL if one-time only.');
            $table->boolean('is_lifetime')->default(false)
                ->comment('If true, this vaccine is given only once in the animal lifetime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccines');
    }
};
