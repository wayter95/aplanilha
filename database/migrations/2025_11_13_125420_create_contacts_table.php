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
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['customer', 'supplier', 'location'])->comment('customer, supplier, location')->default('customer');
            $table->uuid('responsible_user_id');
            $table->uuid('client_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('name_line')->nullable();
            $table->string('website')->nullable();

            // Visiting address
            $table->string('street_visiting')->nullable();
            $table->string('house_number_visiting')->nullable();
            $table->string('postal_code_visiting')->nullable();
            $table->string('city_visiting')->nullable();
            $table->string('state_visiting')->nullable();
            $table->string('country_visiting')->nullable();
            $table->decimal('lat_visiting', 10, 8)->nullable();
            $table->decimal('lng_visiting', 11, 8)->nullable();

            // Mailing address
            $table->string('street_mailing')->nullable();
            $table->string('house_number_mailing')->nullable();
            $table->string('postal_code_mailing')->nullable();
            $table->string('city_mailing')->nullable();
            $table->string('state_mailing')->nullable();
            $table->string('country_mailing')->nullable();
            $table->decimal('lat_mailing', 10, 8)->nullable();
            $table->decimal('lng_mailing', 11, 8)->nullable();

            // Billing address
            $table->string('street_billing')->nullable();
            $table->string('house_number_billing')->nullable();
            $table->string('postal_code_billing')->nullable();
            $table->string('city_billing')->nullable();
            $table->string('state_billing')->nullable();
            $table->string('country_billing')->nullable();
            $table->decimal('lat_billing', 10, 8)->nullable();
            $table->decimal('lng_billing', 11, 8)->nullable();
            
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('client_id', 'fk_client_contacts')
                ->references('id')
                ->on('subscribed_clients')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('responsible_user_id', 'fk_responsible_user_contacts')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
