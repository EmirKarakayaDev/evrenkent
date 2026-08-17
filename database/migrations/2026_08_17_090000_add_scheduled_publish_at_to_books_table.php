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
            // Yazarın önerdiği/adminin onaylarken kesinleştirdiği hedef yayın tarihi.
            // "Onaylandı" durumundaki bir kitapta doluysa "Yakında Çıkacaklar" rafında
            // teaser olarak görünür; php artisan books:publish-scheduled bu tarih
            // geldiğinde kitabı otomatik "Yayında"ya çevirir.
            $table->dateTime('scheduled_publish_at')->nullable()->after('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('scheduled_publish_at');
        });
    }
};
