<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('author', 255);
            $table->text('description')->nullable();
            $table->year('publication_year');
            $table->foreignId('genre_id')->constrained()->onDelete('cascade');
            $table->string('cover_image')->nullable();
            $table->decimal('average_rating', 3, 2)->nullable()->default(0);
            $table->string('ISBN', 17)->unique()->nullable();
            $table->enum('status', ['available', 'borrowed', 'reserved'])->default('available');
            $table->softDeletes();
            $table->timestamps();

            $table->index('title');
            $table->index('author');
            $table->index('status');
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
