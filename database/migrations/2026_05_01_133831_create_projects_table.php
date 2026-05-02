<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->enum('status', ['planned', 'active', 'completed', 'paused', 'cancelled'])->default('planned');
            $table->enum('billing_type', ['paid', 'free', 'community', 'internal'])->default('paid');
            $table->string('currency', 3)->default('MYR');
            $table->decimal('agreed_amount', 15, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('live_url')->nullable();
            $table->string('repository_url')->nullable();
            $table->string('admin_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'billing_type']);
            $table->index(['client_id', 'status']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
