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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('status', ['active', 'pending', 'cancelled', 'completed'])->comment('active, pending, cancelled, completed')->default('active');
            $table->string('name');
            $table->string('project_number');
            $table->string('uf_project')->nullable();
            $table->uuid('project_parent_id')->nullable();
            $table->uuid('client_id');
            $table->uuid('project_types_id');
            $table->uuid('responsible_user_id');
            $table->uuid('user_manager_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->uuid('client_contact_id')->nullable();
            $table->uuid('location_contact_id')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Foreign keys
            $table->foreign('client_id', 'fk_subscribed_client_projects')
                ->references('id')
                ->on('client_subscribes')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('project_types_id', 'fk_project_types_projects')
                ->references('id')
                ->on('project_types')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('responsible_user_id', 'fk_responsible_user_projects')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('user_manager_id', 'fk_user_manager_projects')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('client_contact_id', 'fk_client_contact_projects')
                ->references('id')
                ->on('contacts')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('location_contact_id', 'fk_location_contact_projects')
                ->references('id')
                ->on('contacts')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign('fk_subscribed_client_projects');
            $table->dropForeign('fk_project_types_projects');
            $table->dropForeign('fk_responsible_user_projects');
            $table->dropForeign('fk_user_manager_projects');
            $table->dropForeign('fk_client_contact_projects');
            $table->dropForeign('fk_location_contact_projects');
        });

        Schema::dropIfExists('projects');
    }
};
