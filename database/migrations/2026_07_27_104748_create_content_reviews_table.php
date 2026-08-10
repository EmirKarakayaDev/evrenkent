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
        Schema::create('content_reviews', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable');
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_reviews');
    }
};
