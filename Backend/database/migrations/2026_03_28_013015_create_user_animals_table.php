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
        Schema::create('user_animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('animal_id')
                ->constrained('animals')
                ->cascadeOnDelete();
            $table->string('nickname')->nullable()
                ->comment('Owner given name for this specific animal');
            $table->date('birth_date')->nullable();
            $table->date('last_vaccine_date')->nullable()
                ->comment('Last date any vaccine was given — used to calculate next schedule');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_animals');
    }
};
