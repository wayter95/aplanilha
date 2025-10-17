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
        
        Schema::create('role_user', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('role_id');
            $table->timestamps();
            
            // Primary key composite
            $table->primary(['user_id', 'role_id']);
            
            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('user_roles')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['user_id']);
            $table->index(['role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
