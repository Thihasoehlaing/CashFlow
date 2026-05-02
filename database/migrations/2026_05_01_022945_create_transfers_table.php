<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('from_account_id')->constrained('accounts')->restrictOnDelete();
            $table->foreignId('to_account_id')->constrained('accounts')->restrictOnDelete();
            $table->decimal('from_amount', 15, 2);
            $table->string('from_currency', 3);
            $table->decimal('to_amount', 15, 2);
            $table->string('to_currency', 3);
            $table->decimal('exchange_rate', 15, 6);
            $table->decimal('fee', 15, 2)->default(0);
            $table->string('fee_currency', 3)->nullable();
            $table->date('date')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
