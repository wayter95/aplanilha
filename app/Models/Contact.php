<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\TenantScope;

class Contact extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'type',
        'responsible_user_id',
        'client_id',
        'name',
        'email',
        'phone',
        'name_line',
        'website',
        'street_visiting',
        'house_number_visiting',
        'postal_code_visiting',
        'city_visiting',
        'state_visiting',
        'country_visiting',
        'lat_visiting',
        'lng_visiting',
        'street_mailing',
        'house_number_mailing',
        'postal_code_mailing',
        'city_mailing',
        'state_mailing',
        'country_mailing',
        'lat_mailing',
        'lng_mailing',
        'street_billing',
        'house_number_billing',
        'postal_code_billing',
        'city_billing',
        'state_billing',
        'country_billing',
        'lat_billing',
        'lng_billing',
        'general_notes',
    ];

    protected $casts = [
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

    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function contactPersons(): HasMany
    {
        return $this->hasMany(ContactPerson::class, 'contact_id');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where(function($q) use ($city) {
            $q->where('city_visiting', $city)
              ->orWhere('city_mailing', $city)
              ->orWhere('city_billing', $city);
        });
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where(function($q) use ($country) {
            $q->where('country_visiting', $country)
              ->orWhere('country_mailing', $country)
              ->orWhere('country_billing', $country);
        });
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'ILIKE', "%{$search}%")
              ->orWhere('email', 'ILIKE', "%{$search}%")
              ->orWhere('phone', 'ILIKE', "%{$search}%")
              ->orWhere('city_visiting', 'ILIKE', "%{$search}%")
              ->orWhere('city_mailing', 'ILIKE', "%{$search}%")
              ->orWhere('city_billing', 'ILIKE', "%{$search}%");
        });
    }

    public function getFullAddressVisitingAttribute(): string
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

    public function getFullAddressMailingAttribute(): string
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

    public function getFullAddressBillingAttribute(): string
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
