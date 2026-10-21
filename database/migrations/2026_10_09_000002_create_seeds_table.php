<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seed_category_id')->constrained('seed_categories')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('name_pa');
            $table->string('name_en');
            $table->string('name_hi')->nullable();
            $table->string('local_names')->nullable();
            $table->string('scientific_name')->nullable();
            
            $table->text('description_en')->nullable();
            $table->text('description_pa')->nullable();
            $table->text('description_hi')->nullable();
            
            $table->string('region_of_origin')->nullable();
            $table->string('growing_season')->nullable();
            $table->string('sowing_period')->nullable();
            $table->string('soil_preferences')->nullable();
            $table->string('water_requirements')->nullable();
            $table->string('crop_duration')->nullable();
            
            $table->text('traditional_uses')->nullable();
            $table->text('culinary_uses')->nullable();
            $table->text('farmer_observations')->nullable();
            $table->text('preservation_methods')->nullable();
            
            $table->string('featured_image')->nullable();
            $table->json('video_links')->nullable();
            
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_verified')->default(false)->index();
            $table->string('publication_status')->default('published')->index(); // published, draft, archived
            
            $table->string('contributor_name')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['name_pa', 'name_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seeds');
    }
};
