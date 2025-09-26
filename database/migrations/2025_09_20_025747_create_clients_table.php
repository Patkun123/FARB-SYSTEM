<?php
/** 2025_09_25_000000_create_clients_and_departments_tables.php **/
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
        // Clients table (companies)
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company')->unique(); // company name
            $table->timestamps();
        });

        // Departments table (linked to a client)
        Schema::create('client_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->onDelete('cascade'); // delete deps if company deleted
            $table->string('department');
            $table->string('email')->unique(); // each dept email
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_departments');
        Schema::dropIfExists('clients');
    }
};
