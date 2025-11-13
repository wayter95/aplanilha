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
        Schema::create('project_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->uuid('subscribed_client_id');
            $table->string('color')->default('#000000');
            $table->enum('status', ['a', 'b'])->comment('a = active, b = blocked')->default('b');

            $table->foreign('subscribed_client_id', 'fk_subscribed_client_projects')
                ->references('id')
                ->on('subscribed_clients')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_types');
    }
};
