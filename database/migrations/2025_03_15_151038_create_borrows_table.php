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
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('borrowed_at')->useCurrent();
            $table->date('due_date');
            $table->date('returned_at')->nullable();
            $table->integer('renewal_count')->default(0);
            $table->decimal('late_fee', 8, 2)->nullable()->default(0);
            $table->enum('status', ['Active', 'Completed', 'Cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('due_date');
            $table->index('returned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
