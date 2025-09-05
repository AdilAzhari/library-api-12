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
        Schema::create('library_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('card_number', 20)->unique();
            $table->date('issued_date');
            $table->date('expires_date');
            $table->enum('status', ['active', 'suspended', 'expired', 'lost', 'cancelled'])
                ->default('active');
            $table->text('qr_code')->nullable();
            $table->string('barcode', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('card_number');
            $table->index('expires_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_cards');
    }
};
