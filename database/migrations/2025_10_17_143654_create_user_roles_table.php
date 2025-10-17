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
        // Enable UUID extension if not already enabled
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
        
        Schema::create('user_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id'); // FK para client_subscribes.id
            $table->string('name'); // ex: 'Master', 'Admin', 'User'
            $table->string('description')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('client_id')->references('id')->on('client_subscribes')->onDelete('cascade');
            
            // Unique constraint: nome deve ser único por client_id
            $table->unique(['client_id', 'name']);
            
            // Indexes for better performance
            $table->index(['client_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
