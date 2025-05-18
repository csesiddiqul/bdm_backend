<?php

use App\Enums\StatusEnum;
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
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string(column: 'image')->index()->nullable();
            $table->string(column: 'video_url')->index()->nullable();
            $table->string('name')->nullable();
            $table->unsignedInteger('sorting_index')->nullable();
            $table->enum('status', array_column(StatusEnum::cases(), 'value'))->default(StatusEnum::Active);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
