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
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
        }
        
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->nullable(); // FK para client_subscribes.id
            $table->uuid('user_id')->nullable(); // FK para users.id
            $table->string('action'); // ex: 'login', 'create_user', 'update_profile'
            $table->string('model_type')->nullable(); // ex: 'User', 'ClientSubscribe'
            $table->uuid('model_id')->nullable(); // ID do modelo afetado
            $table->json('old_values')->nullable(); // Valores anteriores
            $table->json('new_values')->nullable(); // Novos valores
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('client_id')->references('id')->on('client_subscribes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index(['client_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
