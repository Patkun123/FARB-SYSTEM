<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * invoices table
         * Each record represents a single invoice document.
         */
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Custom invoice number in format "SI 1710"
            $table->string('invoice_number')->unique();

            // Relation to statement of account (for dumping statement_text and total_amount_due)
            $table->foreignId('statement_id')
                ->nullable()
                ->constrained('statements_of_account')
                ->onDelete('set null');

            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('client_department_id')->nullable()->constrained('client_departments')->onDelete('set null');

            $table->date('invoice_date');
            $table->string('internal_department')->nullable();

            // When invoice is generated from a statement of account
            $table->text('description')->nullable(); // dumps statement_text here
            $table->decimal('total_amount', 12, 2)->default(0.00); // dumps total_amount_due here

            // Status: pending, paid, void
            $table->enum('status', ['pending', 'paid', 'void'])->default('pending');

            $table->timestamps();
        });

        /**
         * invoice_items table
         * Contains detailed line items per invoice.
         */
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->integer('qty')->default(0);
            $table->string('unit', 50)->nullable();
            $table->text('description');
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->timestamps();
        });

        /**
         * Create a trigger or auto-format generator for invoice_number like "SI 1710"
         * (handled in DB or model, here’s an example of DB default behavior)
         */
        DB::unprepared("
            CREATE TRIGGER tr_invoices_before_insert
            BEFORE INSERT ON invoices
            FOR EACH ROW
            BEGIN
                DECLARE next_number INT;
                SELECT IFNULL(MAX(CAST(SUBSTRING(invoice_number, 4) AS UNSIGNED)), 1709) + 1 INTO next_number FROM invoices;
                SET NEW.invoice_number = CONCAT('SI ', next_number);
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS tr_invoices_before_insert");
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
