<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Page extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'meta_description',
        'body',
        'template',
        'template_layout',
        'parent_id',
        'sort_order',
        'is_published',
        'is_home',
        'seo',
        'metadata',
        'og_image',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_home' => 'boolean',
        'seo' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeHome($query)
    {
        return $query->where('is_home', true);
    }

    /**
     * Get available templates for dropdown selection
     */
    public static function getAvailableTemplates(): array
    {
        return [
            'minimalist-legal' => 'Minimalist Legal/Info',
            'classic-grid' => 'Classic Blog Grid',
            'editorial-news' => 'Editorial News & Announcements',
            'donation-campaign' => 'Donation Campaign Page',
            'event-hub' => 'Event & Volunteer Hub',
        ];
    }
}
