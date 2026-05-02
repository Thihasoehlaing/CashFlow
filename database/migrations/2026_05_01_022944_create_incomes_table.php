<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income', function (Blueprint $table): void {
            $table->id();
            $table->enum('source', ['job', 'family', 'freelance'])->index();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('project_name')->nullable();
            $table->enum('billing_type', ['fixed', 'hourly'])->nullable();
            $table->decimal('hours', 8, 2)->nullable();
            $table->decimal('rate_per_hour', 15, 2)->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->index();
            $table->decimal('amount_in_myr', 15, 2);
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->string('description')->nullable();
            $table->date('date')->index();
            $table->enum('type', ['personal', 'business'])->default('personal')->index();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('income');
    }
};
