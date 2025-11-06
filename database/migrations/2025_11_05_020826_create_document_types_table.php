<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->nullable();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['client_id', 'code']);
            $table->index(['client_id', 'is_active']);
            $table->index('code');
            
            if (Schema::hasTable('client_subscribes')) {
                $table->foreign('client_id')->references('id')->on('client_subscribes')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
