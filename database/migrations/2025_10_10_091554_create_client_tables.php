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
        // Clients table (companies)
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company')->unique(); // company name
            $table->timestamps();
        });

       // Updated Client Departments table
        if (!Schema::hasTable('client_departments')) {
            Schema::create('client_departments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')
                      ->constrained('clients')
                      ->onDelete('cascade');
                $table->string('department');
                $table->string('email')->unique();
                $table->string('personnel');
                $table->string('position');
                $table->timestamps();
            });
        } else {
            Schema::table('client_departments', function (Blueprint $table) {
                if (!Schema::hasColumn('client_departments', 'personnel')) {
                    $table->string('personnel')->after('email');
                }
                if (!Schema::hasColumn('client_departments', 'position')) {
                    $table->string('position')->after('personnel');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_departments', function (Blueprint $table) {
            if (Schema::hasColumn('client_departments', 'personnel')) {
                $table->dropColumn('personnel');
            }
            if (Schema::hasColumn('client_departments', 'position')) {
                $table->dropColumn('position');
            }
        });
    }
};
