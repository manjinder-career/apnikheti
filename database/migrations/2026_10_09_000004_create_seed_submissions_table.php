<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seed_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('contributor_name');
            $table->string('village')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            
            $table->string('seed_name_pa');
            $table->string('seed_name_en')->nullable();
            $table->string('crop_category_name')->nullable();
            $table->string('local_names')->nullable();
            
            $table->text('description')->nullable();
            $table->text('traditional_uses')->nullable();
            $table->string('growing_season')->nullable();
            $table->text('preservation_method')->nullable();
            $table->string('duration_known')->nullable();
            
            $table->string('photo_path')->nullable();
            $table->boolean('contact_consent')->default(false);
            $table->boolean('display_consent')->default(true);
            
            $table->string('status')->default('pending')->index(); // pending, approved, rejected, archived
            $table->text('review_notes')->nullable();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seed_submissions');
    }
};
