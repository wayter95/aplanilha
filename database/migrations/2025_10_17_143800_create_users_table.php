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
        // Enable UUID extension only for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
        }
        
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->nullable(); // FK para client_subscribes.id, null = usuário Master
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('avatar_path')->nullable();
            $table->boolean('is_master')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            // Unique constraint: email deve ser único por client_id
            $table->unique(['client_id', 'email']);
            
            // Foreign key constraint
            $table->foreign('client_id')->references('id')->on('client_subscribes')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['client_id', 'email']);
            $table->index(['is_master']);
            $table->index(['email']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->uuid('client_id');
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            $table->unique(['client_id', 'email']);
            $table->foreign('client_id')->references('id')->on('client_subscribes')->onDelete('cascade');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
