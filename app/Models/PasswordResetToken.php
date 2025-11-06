<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\TenantScope;

class PasswordResetToken extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'password_reset_tokens';

    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = [
        'client_id',
        'email',
        'token',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    public function client()
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }

    public function scopeForEmail($query, $email)
    {
        return $query->where('email', $email);
    }
}