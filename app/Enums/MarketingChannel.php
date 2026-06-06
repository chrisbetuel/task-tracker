<?php

namespace App\Enums;

enum MarketingChannel: string
{
    case Email = 'email';
    case Social = 'social';
    case PaidAds = 'paid_ads';
    case SEO = 'seo';
    case General = 'general';

    public function label(): string
    {
        return match ($this) {
            self::Email => 'Email',
            self::Social => 'Social Media',
            self::PaidAds => 'Paid Ads',
            self::SEO => 'SEO',
            self::General => 'General',
        };
    }
}
