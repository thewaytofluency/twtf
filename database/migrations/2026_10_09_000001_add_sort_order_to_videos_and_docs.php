<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['videos', 'docs'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('required_access_level');
            });

            // Keep today's natural order (oldest first) as the starting lesson order.
            DB::table($table)->update(['sort_order' => DB::raw('id')]);
        }
    }

    public function down(): void
    {
        foreach (['videos', 'docs'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};
