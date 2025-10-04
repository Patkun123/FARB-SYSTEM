<?php

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
        Schema::create('billing_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('summary_name');
            $table->string('department_name')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        Schema::create('billing_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_summary_id')->constrained()->cascadeOnDelete();
            $table->decimal('reg_hr', 10, 2)->default(0);
            $table->decimal('ot', 10, 2)->default(0);
            $table->decimal('np', 10, 2)->default(0);
            $table->decimal('hpnp', 10, 2)->default(0);
            $table->decimal('reg_hol', 10, 2)->default(0);
            $table->decimal('spec_hol', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('billing_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_summary_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('manual_override')->default(false);
            $table->boolean('use_custom')->default(false);

            // Computed summary fields
            $table->decimal('reg_hr', 10, 2)->default(0);
            $table->decimal('ot', 10, 2)->default(0);
            $table->decimal('np', 10, 2)->default(0);
            $table->decimal('hpnp', 10, 2)->default(0);
            $table->decimal('reg_hol', 10, 2)->default(0);
            $table->decimal('spec_hol', 10, 2)->default(0);

            $table->timestamps();
        });

        Schema::create('employee_custom_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('billing_employees')->cascadeOnDelete();
            $table->decimal('reg_hr', 10, 2)->nullable();
            $table->decimal('ot', 10, 2)->nullable();
            $table->decimal('np', 10, 2)->nullable();
            $table->decimal('hpnp', 10, 2)->nullable();
            $table->decimal('reg_hol', 10, 2)->nullable();
            $table->decimal('spec_hol', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('billing_days_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_summary_id')->constrained()->cascadeOnDelete();
            $table->date('day_date');
            $table->enum('type', ['work','reg_hol','spec_hol','np','hpnp'])->default('work');
            $table->integer('threshold')->default(8);
            $table->timestamps();
        });

        Schema::create('employee_daily_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('billing_employees')->cascadeOnDelete();
            $table->foreignId('day_meta_id')->constrained('billing_days_meta')->cascadeOnDelete();
            $table->decimal('hours', 10, 2)->default(0);
            $table->enum('override_type', ['','work','reg_hol','spec_hol','np','hpnp'])->nullable();
            $table->integer('override_threshold')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'day_meta_id']);
        });

        // billing overall totals table
        Schema::create('billing_totals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_summary_id')->constrained()->cascadeOnDelete();
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('ot_total', 15, 2)->default(0);
            $table->decimal('np_total', 15, 2)->default(0);
            $table->decimal('hpnp_total', 15, 2)->default(0);
            $table->decimal('reg_hol_total', 15, 2)->default(0);
            $table->decimal('spec_hol_total', 15, 2)->default(0);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_totals');
        Schema::dropIfExists('employee_daily_entries');
        Schema::dropIfExists('billing_days_meta');
        Schema::dropIfExists('employee_custom_rates');
        Schema::dropIfExists('billing_employees');
        Schema::dropIfExists('billing_rates');
        Schema::dropIfExists('billing_summaries');
    }
};
