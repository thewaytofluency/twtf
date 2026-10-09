<?php

namespace Database\Seeders;

use App\Models\ImpactItem;
use Illuminate\Database\Seeder;

class ImpactItemSeeder extends Seeder
{
    /** The three impact cards the landing page originally hard-coded (empty table only). */
    public function run(): void
    {
        if (ImpactItem::exists()) {
            return;
        }

        $items = [
            ['Empowering Communities', 'Helping communities grow through education and resources.', '🤝'],
            ['Transforming Lives', 'Providing opportunities to individuals for a better future.', '🚀'],
            ['Global Reach', 'Making a difference across the globe with our initiatives.', '🌍'],
        ];

        foreach ($items as $i => [$title, $description, $emoji]) {
            ImpactItem::create([
                'title' => $title,
                'description' => $description,
                'emoji' => $emoji,
                'sort_order' => $i + 1,
            ]);
        }
    }
}
