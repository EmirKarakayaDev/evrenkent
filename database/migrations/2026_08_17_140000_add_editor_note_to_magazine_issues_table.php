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
        Schema::table('magazine_issues', function (Blueprint $table) {
            // Dergi editörünün sayı için yazdığı editör yazısı — hem dergi sayısı
            // tanıtım sayfasında gösterilebilir hem de editörün dashboard'undaki
            // "hazırlık" checklist'inin gerçek bir maddesi olur (doldu mu/dolmadı mı).
            $table->text('editor_note')->nullable()->after('cover_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magazine_issues', function (Blueprint $table) {
            $table->dropColumn('editor_note');
        });
    }
};
