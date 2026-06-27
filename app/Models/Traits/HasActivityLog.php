<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasActivityLog
{
    public function activities(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    public function logActivity(string $description, ?string $type = null): ActivityLog
    {
        return $this->activities()->create([
            'description' => $description,
            'type' => $type ?? 'info',
            'user_id' => auth()->id(),
        ]);
    }
}
