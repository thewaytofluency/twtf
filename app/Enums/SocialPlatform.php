<?php

namespace App\Enums;

enum SocialPlatform: string
{
    case Facebook = 'facebook';
    case YouTube = 'youtube';
    case Instagram = 'instagram';
    case TikTok = 'tiktok';
    case WhatsApp = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::Facebook => 'Facebook',
            self::YouTube => 'YouTube',
            self::Instagram => 'Instagram',
            self::TikTok => 'TikTok',
            self::WhatsApp => 'WhatsApp',
        };
    }
}
