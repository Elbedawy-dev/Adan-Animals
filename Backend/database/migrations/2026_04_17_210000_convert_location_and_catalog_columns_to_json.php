<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->convertStringColumnToJson('countries', 'name');
        $this->convertStringColumnToJson('governorates', 'name');
        $this->convertStringColumnToJson('cities', 'name');
        $this->convertStringColumnToJson('regions', 'name');
        $this->convertStringColumnToJson('animal_categories', 'name');
        $this->convertTextColumnToJson('animal_categories', 'description');
        $this->convertStringColumnToJson('animals', 'name');
        $this->convertTextColumnToJson('animals', 'description');
        $this->convertStringColumnToJson('vaccines', 'name');
    }

    public function down(): void
    {
        $this->revertJsonColumnToString('countries', 'name');
        $this->revertJsonColumnToString('governorates', 'name');
        $this->revertJsonColumnToString('cities', 'name');
        $this->revertJsonColumnToString('regions', 'name');
        $this->revertJsonColumnToString('animal_categories', 'name');
        $this->revertJsonDescriptionToText('animal_categories', 'description');
        $this->revertJsonColumnToString('animals', 'name');
        $this->revertJsonDescriptionToText('animals', 'description');
        $this->revertJsonColumnToString('vaccines', 'name');
    }

    private function convertStringColumnToJson(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $rows = DB::table($table)->select('id', $column)->get();

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->dropColumn($column);
        });

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->json($column);
        });

        foreach ($rows as $row) {
            $value = $row->{$column};
            $payload = is_string($value) && $value !== ''
                ? ['en' => $value, 'ar' => $value]
                : ['en' => '', 'ar' => ''];
            DB::table($table)->where('id', $row->id)->update([
                $column => json_encode($payload, JSON_UNESCAPED_UNICODE),
            ]);
        }
    }

    private function convertTextColumnToJson(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $rows = DB::table($table)->select('id', $column)->get();

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->dropColumn($column);
        });

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->json($column)->nullable();
        });

        foreach ($rows as $row) {
            $value = $row->{$column};
            if ($value === null || $value === '') {
                DB::table($table)->where('id', $row->id)->update([$column => null]);

                continue;
            }
            $payload = is_string($value)
                ? ['en' => $value, 'ar' => $value]
                : ['en' => '', 'ar' => ''];
            DB::table($table)->where('id', $row->id)->update([
                $column => json_encode($payload, JSON_UNESCAPED_UNICODE),
            ]);
        }
    }

    private function revertJsonColumnToString(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $rows = DB::table($table)->select('id', $column)->get();

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->dropColumn($column);
        });

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->string($column);
        });

        foreach ($rows as $row) {
            $raw = $row->{$column};
            $decoded = is_string($raw) ? json_decode($raw, true) : (array) $raw;
            $en = is_array($decoded) ? ($decoded['en'] ?? reset($decoded) ?? '') : (string) $raw;
            DB::table($table)->where('id', $row->id)->update([$column => $en]);
        }
    }

    private function revertJsonDescriptionToText(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $rows = DB::table($table)->select('id', $column)->get();

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->dropColumn($column);
        });

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->text($column)->nullable();
        });

        foreach ($rows as $row) {
            $raw = $row->{$column};
            if ($raw === null) {
                DB::table($table)->where('id', $row->id)->update([$column => null]);

                continue;
            }
            $decoded = is_string($raw) ? json_decode($raw, true) : (array) $raw;
            $en = is_array($decoded) ? ($decoded['en'] ?? '') : (string) $raw;
            DB::table($table)->where('id', $row->id)->update([$column => $en]);
        }
    }
};
