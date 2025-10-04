<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        /** 1️⃣ Statements of Accounts (SOA Header) */
        Schema::create('statements_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('client_departments')->cascadeOnDelete();
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('due_date');
            $table->string('personnel_name');
            $table->string('position');
            $table->text('statement_text')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->timestamps();
        });

        /** 2️⃣ Pivot table linking SOA ↔ Billing Summaries */
        Schema::create('soa_billing_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soa_id')->constrained('statements_of_accounts')->cascadeOnDelete();
            $table->foreignId('billing_summary_id')->constrained('billing_summaries')->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['soa_id', 'billing_summary_id']); // prevent duplicates
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soa_billing_summaries');
        Schema::dropIfExists('statements_of_accounts');
    }
};
