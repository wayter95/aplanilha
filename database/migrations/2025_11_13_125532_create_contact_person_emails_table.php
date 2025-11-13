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
        Schema::create('contact_person_emails', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contact_person_id');
            $table->string('email');
            $table->timestamps();
            
            $table->foreign('contact_person_id')
                  ->references('id')
                  ->on('contact_person')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_person_emails');
    }
};
