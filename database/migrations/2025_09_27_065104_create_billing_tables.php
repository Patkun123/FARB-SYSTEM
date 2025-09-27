<?php
// database/migrations/2025_09_27_000000_create_billing_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('billing_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('summary_name');
            $table->string('department_name')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_summary_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->boolean('use_custom')->default(false);
            $table->boolean('manual_override')->default(false);
            $table->json('custom_rates')->nullable(); // {reg_hr, ot, np, ...}
            $table->json('daily')->nullable();        // daily hours array
            $table->json('day_overrides')->nullable();// per-day override type
            $table->json('day_thresholds')->nullable();// per-day thresholds
            $table->decimal('reg_hr',8,2)->default(0);
            $table->decimal('ot',8,2)->default(0);
            $table->decimal('np',8,2)->default(0);
            $table->decimal('hpnp',8,2)->default(0);
            $table->decimal('reg_hol',8,2)->default(0);
            $table->decimal('spec_hol',8,2)->default(0);
            $table->timestamps();
        });

        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_summary_id')->constrained()->onDelete('cascade');
            $table->string('key'); // reg_hr, ot, np, hpnp, reg_hol, spec_hol
            $table->decimal('rate',8,2);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rates');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('billing_summaries');
    }
};
