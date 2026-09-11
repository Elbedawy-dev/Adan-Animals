<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vaccines', function (Blueprint $table) {
            $table->dropConstrainedForeignId('animal_id');

            $table->foreignId('animal_category_id')
                ->after('id')
                ->constrained('animal_categories')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vaccines', function (Blueprint $table) {
            $table->dropConstrainedForeignId('animal_category_id');

            $table->foreignId('animal_id')
                ->after('id')
                ->constrained('animals')
                ->cascadeOnDelete();
        });
    }
};
