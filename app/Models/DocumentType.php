<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'document_types';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'client_id',
        'code',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\ClientSubscribe::class, 'client_id');
    }

    public function templates()
    {
        return $this->hasMany(DocumentTemplate::class, 'type', 'code');
    }
}

