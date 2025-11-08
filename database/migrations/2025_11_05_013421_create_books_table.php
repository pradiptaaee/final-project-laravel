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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->index();
            $table->string('isbn', 50)->nullable()->index();
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete()->index();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete()->index();
            $table->year('publication_year')->nullable()->index();
            $table->enum('status', ['available', 'rented', 'reserved'])->default('available')->index();
            $table->string('location', 191)->nullable()->index();
            $table->timestamps();

            // Index untuk optimisasi filter dan pencarian
            // $table->index(['title', 'author_id', 'category_id']);
            // $table->index(['availability_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }

    
};
