<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_category_id')->constrained('article_categories')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('title_en');
            $table->string('title_pa');
            $table->string('title_hi')->nullable();
            
            $table->text('excerpt_en')->nullable();
            $table->text('excerpt_pa')->nullable();
            $table->text('excerpt_hi')->nullable();
            
            $table->longText('content_en')->nullable();
            $table->longText('content_pa')->nullable();
            $table->longText('content_hi')->nullable();
            
            $table->string('author_name')->default('Apni Kheti Team');
            $table->string('featured_image')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_published')->default(true)->index();
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
