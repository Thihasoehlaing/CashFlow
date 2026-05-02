<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
            $table->string('name');
            $table->enum('type', ['domain', 'server', 'email', 'plugin', 'maintenance', 'other'])->default('other');
            $table->string('provider')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('MYR');
            $table->decimal('amount_in_myr', 15, 2);
            $table->enum('billing_cycle', ['one_time', 'monthly', 'yearly'])->default('one_time');
            $table->date('next_renewal_date')->nullable();
            $table->boolean('is_billable')->default(false);
            $table->boolean('auto_log_expense')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'type']);
            $table->index('next_renewal_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_costs');
    }
};
