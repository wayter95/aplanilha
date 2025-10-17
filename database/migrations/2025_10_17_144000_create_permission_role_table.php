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
        
        Schema::create('permission_role', function (Blueprint $table) {
            $table->uuid('role_id');
            $table->uuid('permission_id');
            $table->timestamps();
            
            // Primary key composite
            $table->primary(['role_id', 'permission_id']);
            
            // Foreign key constraints
            $table->foreign('role_id')->references('id')->on('user_roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('user_permissions')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['role_id']);
            $table->index(['permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
