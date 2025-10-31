<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\DocumentType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->nullable();
            $table->string('type');
            $table->string('name');
            $table->string('language')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active');
            $table->text('header_html')->nullable();
            $table->text('footer_html')->nullable();
            $table->longText('content_html');
            $table->json('layout_json')->nullable();
            $table->string('background_image_path')->nullable();
            $table->json('fonts_json')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'type']);
            $table->foreign('client_id')->references('id')->on('client_subscribes');
        });

        DB::statement("CREATE UNIQUE INDEX document_templates_unique_default_per_client_type ON document_templates (client_id, type) WHERE is_default = true");
        DB::statement("CREATE INDEX document_templates_type_idx ON document_templates (type)");
        DB::statement("CREATE INDEX document_templates_status_idx ON document_templates (status)");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS document_templates_unique_default_per_client_type');
        DB::statement('DROP INDEX IF EXISTS document_templates_type_idx');
        DB::statement('DROP INDEX IF EXISTS document_templates_status_idx');
        Schema::dropIfExists('document_templates');
    }
};


