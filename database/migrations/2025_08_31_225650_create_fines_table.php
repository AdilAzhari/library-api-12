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
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('borrow_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('reason', ['overdue', 'lost_book', 'damaged_book', 'late_return', 'processing_fee', 'other'])
                ->default('overdue');
            $table->enum('status', ['pending', 'paid', 'partial', 'waived', 'cancelled'])
                ->default('pending');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->foreignId('waived_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('waived_at')->nullable();
            $table->string('waiver_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'due_date']);
            $table->index(['reason', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
