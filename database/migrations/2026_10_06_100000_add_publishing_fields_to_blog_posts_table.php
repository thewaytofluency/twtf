<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('slug');
            $table->string('cover_image')->nullable()->after('excerpt');
            $table->string('status')->default('published')->after('content');
            $table->dateTime('published_at')->nullable()->after('status');

            $table->index(['status', 'published_at']);
        });

        // Everything that exists today is already live: publish it at its original date.
        DB::table('blog_posts')->update(['published_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex(['status', 'published_at']);
            $table->dropColumn(['excerpt', 'cover_image', 'status', 'published_at']);
        });
    }
};
