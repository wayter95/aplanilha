<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactPersonNote extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'contact_person_id',
        'name',
        'content',
        'note_date',
        'created_by',
    ];

    protected $casts = [
        'note_date' => 'datetime',
    ];

    public function contactPerson(): BelongsTo
    {
        return $this->belongsTo(ContactPerson::class, 'contact_person_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
