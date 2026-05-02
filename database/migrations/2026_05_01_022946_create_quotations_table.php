<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table): void {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('project_title');
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft')->index();
            $table->string('currency', 3);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->enum('discount_type', ['flat', 'percentage'])->nullable();
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->date('issue_date')->index();
            $table->date('valid_until')->index();
            $table->text('notes')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->json('business_snapshot');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
