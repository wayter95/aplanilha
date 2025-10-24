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
        Schema::table('users', function (Blueprint $table) {
            // Remover campos obsoletos relacionados a fotos
            $table->dropColumn(['photo', 'avatar_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Recriar campos se necessário fazer rollback
            $table->string('photo')->nullable();
            $table->string('avatar_path')->nullable();
        });
    }
};
