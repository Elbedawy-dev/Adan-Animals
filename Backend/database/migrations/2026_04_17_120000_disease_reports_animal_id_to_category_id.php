<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disease_reports', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('user_id')
                ->constrained('animal_categories')
                ->cascadeOnDelete();
        });

        foreach (DB::table('disease_reports')->select('id', 'animal_id')->cursor() as $row) {
            $categoryId = DB::table('animals')->where('id', $row->animal_id)->value('category_id');
            if ($categoryId) {
                DB::table('disease_reports')->where('id', $row->id)->update(['category_id' => $categoryId]);
            }
        }

        $fallback = DB::table('animal_categories')->orderBy('id')->value('id');
        if ($fallback) {
            DB::table('disease_reports')->whereNull('category_id')->update(['category_id' => $fallback]);
        }

        Schema::table('disease_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('animal_id');
        });
    }

    public function down(): void
    {
        Schema::table('disease_reports', function (Blueprint $table) {
            $table->foreignId('animal_id')
                ->nullable()
                ->after('user_id')
                ->constrained('animals')
                ->cascadeOnDelete();
        });

        foreach (DB::table('disease_reports')->select('id', 'category_id')->cursor() as $row) {
            $animalId = DB::table('animals')
                ->where('category_id', $row->category_id)
                ->orderBy('id')
                ->value('id');
            if ($animalId) {
                DB::table('disease_reports')->where('id', $row->id)->update(['animal_id' => $animalId]);
            }
        }

        Schema::table('disease_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
