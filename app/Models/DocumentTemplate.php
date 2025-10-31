<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'document_templates';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'client_id',
        'type',
        'name',
        'language',
        'country',
        'is_default',
        'is_system',
        'status',
        'header_html',
        'footer_html',
        'content_html',
        'layout_json',
        'background_image_path',
        'fonts_json',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    public function client()
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }
}


