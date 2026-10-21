<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seed_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seed_id')->constrained('seeds')->onDelete('cascade');
            $table->string('photo_path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seed_photos');
    }
};
