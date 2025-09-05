<?php

declare(strict_types=1);

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
        Schema::create('reading_list_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reading_list_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->timestamp('added_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->integer('priority')->default(1); // 1-5 priority
            $table->timestamps();

            $table->unique(['reading_list_id', 'book_id']);
            $table->index(['reading_list_id', 'added_at']);
            $table->index(['book_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_list_books');
    }
};
