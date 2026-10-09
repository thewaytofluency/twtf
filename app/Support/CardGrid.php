<?php

namespace App\Support;

/**
 * Landing page card sizing that stays tidy for any number of cards. Rows are centered
 * flex-wrap (a short last row sits in the middle), and the card width depends on the count:
 * four cards form a 2x2 grid on tablets and a single row on wide screens, anything else uses
 * a comfortable fixed width. Full class names so Tailwind's scanner sees them.
 */
class CardGrid
{
    public const CONTAINER = 'flex flex-wrap justify-center gap-8 mt-12';

    public static function cardWidth(int $count): string
    {
        return 'w-full '.match ($count) {
            4 => 'md:w-[calc(50%-1rem)] xl:w-[calc(25%-1.5rem)]',
            default => 'md:w-[22rem]',
        };
    }
}
