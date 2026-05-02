<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('status');
            $table->bigInteger('amount');
            $table->foreignId('wallet_from_id')->nullable()->constrained('wallets')->nullOnDelete();
            $table->foreignId('wallet_to_id')->nullable()->constrained('wallets')->nullOnDelete();
            $table->foreignId('reversed_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['wallet_from_id', 'created_at']);
            $table->index(['wallet_to_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
