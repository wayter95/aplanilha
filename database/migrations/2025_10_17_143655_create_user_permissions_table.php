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
        
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id'); // FK para client_subscribes.id
            $table->string('module'); // ex: 'users', 'payments', 'reports'
            $table->string('action'); // ex: 'create', 'edit', 'view', 'delete'
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('client_id')->references('id')->on('client_subscribes')->onDelete('cascade');
            
            // Unique constraint: module + action deve ser único por client_id
            $table->unique(['client_id', 'module', 'action']);
            
            // Indexes for better performance
            $table->index(['client_id', 'module']);
            $table->index(['client_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
