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
        Schema::table('reading_list_items', function (Blueprint $table) {
            $table->unsignedInteger('last_chapter_number')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reading_list_items', function (Blueprint $table) {
            $table->dropColumn('last_chapter_number');
        });
    }
};
