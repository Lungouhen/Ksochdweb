<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait SoftDeletesWithAuthor
{
    use SoftDeletes;

    public static function bootSoftDeletesWithAuthor()
    {
        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by = auth()->id();
                $model->save();
            }
        });
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
