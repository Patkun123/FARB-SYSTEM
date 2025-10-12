<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
 Schema::create('statements_of_account', function (Blueprint $table) {
            $table->id();
            $table->string('soa_title');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('client_departments')->onDelete('cascade');
            $table->date('covered_start_date');
            $table->date('covered_end_date');
            $table->date('due_date');
            $table->string('personnel_name')->nullable();
            $table->string('position')->nullable();
            $table->text('statement_text')->nullable();
            $table->decimal('total_amount_due', 12, 2)->default(0);
            $table->timestamps();
        });
         Schema::create('statement_summary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statement_id')
                ->constrained('statements_of_account')
                ->onDelete('cascade');
            $table->foreignId('billing_summary_id')
                ->constrained('billing_summaries')
                ->onDelete('cascade');
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('statement_summary_items');
        Schema::dropIfExists('statements_of_account');
    }
};
