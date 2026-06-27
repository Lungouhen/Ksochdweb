<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['file_name', 'original_name', 'mime_type', 'disk', 'path', 'size', 'width', 'height', 'collection_name', 'custom_properties', 'uploaded_by'];
    protected $casts = ['custom_properties' => 'array'];

    public function mediable(): MorphTo { return $this->morphTo(); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}
