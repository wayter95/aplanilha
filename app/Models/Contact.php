<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'contacts';

    protected $fillable = [
        'type',
        'responsible_user_id',
        'client_id',
        'name',
        'email',
        'phone',
        'name_line',
        'website',
        // Visiting address
        'street_visiting',
        'house_number_visiting',
        'postal_code_visiting',
        'city_visiting',
        'state_visiting',
        'country_visiting',
        'lat_visiting',
        'lng_visiting',
        // Mailing address
        'street_mailing',
        'house_number_mailing',
        'postal_code_mailing',
        'city_mailing',
        'state_mailing',
        'country_mailing',
        'lat_mailing',
        'lng_mailing',
        // Billing address
        'street_billing',
        'house_number_billing',
        'postal_code_billing',
        'city_billing',
        'state_billing',
        'country_billing',
        'lat_billing',
        'lng_billing',
    ];

    protected $casts = [
        'type' => 'string',
        'lat_visiting' => 'decimal:8',
        'lng_visiting' => 'decimal:8',
        'lat_mailing' => 'decimal:8',
        'lng_mailing' => 'decimal:8',
        'lat_billing' => 'decimal:8',
        'lng_billing' => 'decimal:8',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Relacionamento com usuário responsável
     */
    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    /**
     * Relacionamento com client_subscribes
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }

    /**
     * Relacionamento com projetos como cliente
     */
    public function projectsAsClient(): HasMany
    {
        return $this->hasMany(Project::class, 'client_contact_id');
    }

    /**
     * Relacionamento com projetos como localização
     */
    public function projectsAsLocation(): HasMany
    {
        return $this->hasMany(Project::class, 'location_contact_id');
    }

    /**
     * Scope para filtrar por tipo cliente
     */
    public function scopeCustomer($query)
    {
        return $query->where('type', 'customer');
    }

    /**
     * Scope para filtrar por tipo fornecedor
     */
    public function scopeSupplier($query)
    {
        return $query->where('type', 'supplier');
    }

    /**
     * Scope para filtrar por tipo localização
     */
    public function scopeLocation($query)
    {
        return $query->where('type', 'location');
    }

    /**
     * Accessor para retornar o tipo formatado
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'customer' => 'Cliente',
            'supplier' => 'Fornecedor',
            'location' => 'Localização',
            default => 'Desconhecido',
        };
    }

    /**
     * Retorna o endereço de visitação completo
     */
    public function getVisitingAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street_visiting,
            $this->house_number_visiting,
            $this->postal_code_visiting,
            $this->city_visiting,
            $this->state_visiting,
            $this->country_visiting,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Retorna o endereço de correspondência completo
     */
    public function getMailingAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street_mailing,
            $this->house_number_mailing,
            $this->postal_code_mailing,
            $this->city_mailing,
            $this->state_mailing,
            $this->country_mailing,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Retorna o endereço de cobrança completo
     */
    public function getBillingAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street_billing,
            $this->house_number_billing,
            $this->postal_code_billing,
            $this->city_billing,
            $this->state_billing,
            $this->country_billing,
        ]);

        return implode(', ', $parts);
    }
}
