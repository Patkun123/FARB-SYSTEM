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
            
            // Comprehensive rate structure
            $table->decimal('regular_day', 10, 2)->default(1.0);
            $table->decimal('regular_ot', 10, 2)->default(1.25);
            $table->decimal('night_premium', 10, 2)->default(1.1);
            $table->decimal('night_premium_ot', 10, 2)->default(1.375);
            $table->decimal('rest_day', 10, 2)->default(1.3);
            $table->decimal('sunday_ot', 10, 2)->default(1.69);
            $table->decimal('sunday_night_premium', 10, 2)->default(1.43);
            $table->decimal('sunday_night_premium_ot', 10, 2)->default(1.859);
            $table->decimal('regular_holiday', 10, 2)->default(2.0);
            $table->decimal('regular_holiday_ot', 10, 2)->default(2.6);
            $table->decimal('reg_hol_night_premium', 10, 2)->default(2.2);
            $table->decimal('reg_hol_night_premium_ot', 10, 2)->default(2.86);
            $table->decimal('unworked_regular_day', 10, 2)->default(1.0);
            
            $table->timestamps();
        });

        Schema::create('billing_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_summary_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('manual_override')->default(false);
            $table->boolean('use_custom')->default(false);

            // Comprehensive computed summary fields
            $table->decimal('regular_day', 10, 2)->default(0);
            $table->decimal('regular_ot', 10, 2)->default(0);
            $table->decimal('night_premium', 10, 2)->default(0);
            $table->decimal('night_premium_ot', 10, 2)->default(0);
            $table->decimal('rest_day', 10, 2)->default(0);
            $table->decimal('sunday_ot', 10, 2)->default(0);
            $table->decimal('sunday_night_premium', 10, 2)->default(0);
            $table->decimal('sunday_night_premium_ot', 10, 2)->default(0);
            $table->decimal('regular_holiday', 10, 2)->default(0);
            $table->decimal('regular_holiday_ot', 10, 2)->default(0);
            $table->decimal('reg_hol_night_premium', 10, 2)->default(0);
            $table->decimal('reg_hol_night_premium_ot', 10, 2)->default(0);
            $table->decimal('unworked_regular_day', 10, 2)->default(0);

            $table->timestamps();
        });

        Schema::create('employee_custom_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('billing_employees')->cascadeOnDelete();
            
            // Comprehensive custom rate structure
            $table->decimal('regular_day', 10, 2)->nullable();
            $table->decimal('regular_ot', 10, 2)->nullable();
            $table->decimal('night_premium', 10, 2)->nullable();
            $table->decimal('night_premium_ot', 10, 2)->nullable();
            $table->decimal('rest_day', 10, 2)->nullable();
            $table->decimal('sunday_ot', 10, 2)->nullable();
            $table->decimal('sunday_night_premium', 10, 2)->nullable();
            $table->decimal('sunday_night_premium_ot', 10, 2)->nullable();
            $table->decimal('regular_holiday', 10, 2)->nullable();
            $table->decimal('regular_holiday_ot', 10, 2)->nullable();
            $table->decimal('reg_hol_night_premium', 10, 2)->nullable();
            $table->decimal('reg_hol_night_premium_ot', 10, 2)->nullable();
            $table->decimal('unworked_regular_day', 10, 2)->nullable();
            
            $table->timestamps();
        });

        Schema::create('billing_days_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_summary_id')->constrained()->cascadeOnDelete();
            $table->date('day_date');
            $table->enum('type', [
                'regular_day',
                'regular_ot', 
                'night_premium',
                'night_premium_ot',
                'rest_day',
                'sunday_ot',
                'sunday_night_premium',
                'sunday_night_premium_ot',
                'regular_holiday',
                'regular_holiday_ot',
                'reg_hol_night_premium',
                'reg_hol_night_premium_ot',
                'unworked_regular_day'
            ])->default('regular_day');
            $table->decimal('threshold', 5, 1)->default(8.0);
            $table->timestamps();
        });

        Schema::create('employee_daily_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('billing_employees')->cascadeOnDelete();
            $table->foreignId('day_meta_id')->constrained('billing_days_meta')->cascadeOnDelete();
            $table->decimal('hours', 10, 2)->default(0);
            $table->enum('override_type', [
                '',
                'regular_day',
                'regular_ot',
                'night_premium',
                'night_premium_ot',
                'rest_day',
                'sunday_ot',
                'sunday_night_premium',
                'sunday_night_premium_ot',
                'regular_holiday',
                'regular_holiday_ot',
                'reg_hol_night_premium',
                'reg_hol_night_premium_ot',
                'unworked_regular_day'
            ])->nullable();
            $table->decimal('override_threshold', 5, 1)->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'day_meta_id']);
        });

        // Billing overall totals table
        Schema::create('billing_totals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_summary_id')->constrained()->cascadeOnDelete();
            $table->decimal('grand_total', 15, 2)->default(0);
            
            // Comprehensive total breakdown
            $table->decimal('regular_day_total', 15, 2)->default(0);
            $table->decimal('regular_ot_total', 15, 2)->default(0);
            $table->decimal('night_premium_total', 15, 2)->default(0);
            $table->decimal('night_premium_ot_total', 15, 2)->default(0);
            $table->decimal('rest_day_total', 15, 2)->default(0);
            $table->decimal('sunday_ot_total', 15, 2)->default(0);
            $table->decimal('sunday_night_premium_total', 15, 2)->default(0);
            $table->decimal('sunday_night_premium_ot_total', 15, 2)->default(0);
            $table->decimal('regular_holiday_total', 15, 2)->default(0);
            $table->decimal('regular_holiday_ot_total', 15, 2)->default(0);
            $table->decimal('reg_hol_night_premium_total', 15, 2)->default(0);
            $table->decimal('reg_hol_night_premium_ot_total', 15, 2)->default(0);
            $table->decimal('unworked_regular_day_total', 15, 2)->default(0);
            
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