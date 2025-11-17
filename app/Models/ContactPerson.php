<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactPerson extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'contact_person';

    protected $fillable = [
        'contact_id',
        'first_name',
        'last_name',
        'mobile',
        'role',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(ContactPersonEmail::class, 'contact_person_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ContactPersonNote::class, 'contact_person_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
