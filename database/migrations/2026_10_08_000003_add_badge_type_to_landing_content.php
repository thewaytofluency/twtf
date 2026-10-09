<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['courses', 'impact_items'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('badge_type')->default('emoji')->after('description');
            });

            // Until now an uploaded image simply replaced the emoji; keep that behaviour.
            DB::table($table)->whereNotNull('image')->update(['badge_type' => 'image']);
        }
    }

    public function down(): void
    {
        foreach (['courses', 'impact_items'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('badge_type');
            });
        }
    }
};
