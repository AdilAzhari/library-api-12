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
        Schema::create('reading_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_default')->default(false);
            $table->string('color', 7)->default('#3B82F6'); // Hex color
            $table->string('icon', 50)->default('book-open');
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
            $table->index(['is_public', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_lists');
    }
};
