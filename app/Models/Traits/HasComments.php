<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasComments
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function addComment(string $content, ?int $userId = null): Comment
    {
        return $this->comments()->create([
            'content' => $content,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }
}
