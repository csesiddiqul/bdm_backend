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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('website_title');
            $table->string('slogan')->nullable();
            $table->string('headerlogo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('location')->nullable();
            $table->string('email')->nullable();

            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('telephone')->nullable();

            $table->text('googlemap')->nullable();
            $table->string('websitelink')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();

            $table->string('newsletter')->nullable();

            $table->text('copyrighttext')->nullable();
            $table->text('tramscondition')->nullable();
            $table->text('privacypolicy')->nullable();

            
            $table->json('count_section')->nullable();
            $table->json('help_section')->nullable();
            $table->json('quick_link')->nullable();




            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
