<?php

namespace Database\Seeders;

use App\Enums\SocialPlatform;
use App\Models\SocialMediaLink;
use Illuminate\Database\Seeder;

class SocialMediaLinkSeeder extends Seeder
{
    /**
     * Seed one row per platform (SocialPlatform is a closed 5-case enum, and
     * `platform` is unique) with placeholder URLs for the admin to edit.
     */
    public function run(): void
    {
        $placeholders = [
            SocialPlatform::Facebook->value => 'https://www.facebook.com',
            SocialPlatform::YouTube->value => 'https://www.youtube.com',
            SocialPlatform::Instagram->value => 'https://www.instagram.com',
            SocialPlatform::TikTok->value => 'https://www.tiktok.com',
            SocialPlatform::WhatsApp->value => 'https://wa.me/000000000',
        ];

        foreach ($placeholders as $platform => $url) {
            SocialMediaLink::updateOrCreate(
                ['platform' => $platform],
                ['url' => $url, 'is_visible' => true]
            );
        }
    }
}
