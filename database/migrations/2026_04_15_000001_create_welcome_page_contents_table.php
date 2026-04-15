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
        Schema::create('welcome_page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('hero_badge');
            $table->string('hero_title');
            $table->string('hero_highlight');
            $table->text('hero_description');
            $table->string('hero_primary_cta_text');
            $table->string('hero_secondary_cta_text');
            $table->string('preview_title');
            $table->text('preview_description');
            $table->string('stories_title');
            $table->text('stories_description');
            $table->string('features_title');
            $table->string('how_it_works_title');
            $table->string('final_cta_title');
            $table->text('final_cta_description');
            $table->string('footer_note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('welcome_page_contents');
    }
};
