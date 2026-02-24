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
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('read_count');
            $table->index(['is_published', 'published_at']);
        });

        Schema::table('article_views', function (Blueprint $table) {
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('read_count')->default(0);
            $table->dropIndex(['is_published', 'published_at']);
        });

        Schema::table('article_views', function (Blueprint $table) {
            $table->dropIndex(['ip_address']);
        });
    }
};
