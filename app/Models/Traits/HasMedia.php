<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMedia
{
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function addMedia(string $path, string $collection = 'default'): Media
    {
        return $this->media()->create([
            'path' => $path,
            'collection_name' => $collection,
            'disk' => config('filesystems.default'),
        ]);
    }

    public function getFirstMedia(string $collection = 'default'): ?Media
    {
        return $this->media->where('collection_name', $collection)->first();
    }

    public function getMedia(string $collection = 'default')
    {
        return $this->media->where('collection_name', $collection);
    }
}
