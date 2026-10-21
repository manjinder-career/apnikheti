<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmer_stories', function (Blueprint $table) {
            $table->id();
            $table->string('farmer_name');
            $table->string('village')->nullable();
            $table->string('district')->nullable();
            $table->string('crop_name')->nullable();
            $table->string('farming_practice')->nullable();
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->string('status')->default('pending')->index(); // pending, published, archived
            $table->boolean('display_consent')->default(true);
            $table->string('contact_info_private')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_stories');
    }
};
