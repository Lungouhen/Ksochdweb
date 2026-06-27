<?php

namespace App\Enums;

enum ContentType: string
{
    case PAGE = 'page';
    case POST = 'post';
    case EVENT = 'event';
    case NEWSLETTER = 'newsletter';
    case CAMPAIGN = 'campaign';
    
    public function label(): string
    {
        return match($this) {
            self::PAGE => 'Page',
            self::POST => 'Blog Post',
            self::EVENT => 'Event',
            self::NEWSLETTER => 'Newsletter',
            self::CAMPAIGN => 'Campaign',
        };
    }
}
