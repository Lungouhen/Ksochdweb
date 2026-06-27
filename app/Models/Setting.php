<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group', 'description', 'is_public', 'updated_by'];
    protected $casts = ['is_public' => 'boolean'];

    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }

    public static function get(string $key, $default = null) {
        $setting = self::where('key', $key)->first();
        return $setting ? self::castValue($setting->value, $setting->type) : $default;
    }

    public static function set(string $key, $value, ?string $type = null): void {
        self::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type ?? 'string', 'updated_by' => auth()->id()]);
    }

    private static function castValue($value, string $type) {
        return match($type) {
            'boolean' => (bool) $value,
            'number' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
