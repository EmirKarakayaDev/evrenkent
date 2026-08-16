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
        Schema::table('books', function (Blueprint $table) {
            // Değerlendirme — gerçek bir yorum/puanlama sistemi gelene kadar Süper
            // Admin'in elle girdiği özet değerler (bkz. BookResource helper metni).
            $table->decimal('average_rating', 3, 2)->nullable()->after('discount_price');
            $table->unsignedInteger('review_count')->nullable()->after('average_rating');

            // İçerik istatistikleri — kitabı yükleyen yazar kendi panelinden girer,
            // hangisi doluysa satın alma sayfasında sadece o gösterilir.
            $table->unsignedInteger('page_count')->nullable()->after('review_count');
            $table->unsignedInteger('document_count')->nullable()->after('page_count');
            $table->unsignedInteger('video_count')->nullable()->after('document_count');
            $table->unsignedInteger('map_count')->nullable()->after('video_count');
            $table->unsignedInteger('author_note_count')->nullable()->after('map_count');
            $table->unsignedInteger('source_count')->nullable()->after('author_note_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'average_rating', 'review_count', 'page_count', 'document_count',
                'video_count', 'map_count', 'author_note_count', 'source_count',
            ]);
        });
    }
};
