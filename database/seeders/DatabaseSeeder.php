<?php

namespace Database\Seeders;

use App\Models\Bouquet;
use App\Models\Flower;
use App\Models\FlowerBatch;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $supplier = Supplier::firstOrCreate(
            ['name' => 'Kebun Lembang Highland'],
            ['contact_name' => 'Maya Pratiwi', 'phone' => '+62 812 0000 1122', 'email' => 'supplier@example.test']
        );

        $flowers = [
            ['name' => 'Mawar', 'variety' => 'Ekuador Pastel', 'category' => 'Bunga Utama', 'quantity' => 45],
            ['name' => 'Hydrangea', 'variety' => 'Soft Pink', 'category' => 'Bunga Utama', 'quantity' => 6],
            ['name' => 'White Lily', 'variety' => 'Casablanca', 'category' => 'Bunga Utama', 'quantity' => 18],
            ['name' => "Baby's Breath", 'variety' => 'Import Belanda', 'category' => 'Filler', 'quantity' => 4],
            ['name' => 'Eucalyptus', 'variety' => 'Cinerea Silver Dollar', 'category' => 'Foliage', 'quantity' => 35],
        ];

        foreach ($flowers as $index => $attributes) {
            $quantity = $attributes['quantity'];
            unset($attributes['quantity']);
            $flower = Flower::firstOrCreate(
                ['name' => $attributes['name'], 'variety' => $attributes['variety']],
                [...$attributes, 'low_stock_threshold' => 10]
            );
            FlowerBatch::firstOrCreate(
                ['batch_code' => 'BATCH-BA-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT)],
                [
                    'flower_id' => $flower->id,
                    'supplier_id' => $supplier->id,
                    'quantity' => $quantity,
                    'purchase_price' => 28000,
                    'cold_room_location' => 'Rak A-0'.($index + 1),
                    'received_at' => now()->toDateString(),
                    'expires_at' => now()->addDays(8)->toDateString(),
                ]
            );
        }

        foreach ([
            ['The Luminary Pastel Bouquet', 'Buket Tangan', 735000, 'A delicate pastel rose bouquet wrapped with ivory organza ribbon.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuClPj4opDsT9XIKcBxxfnk0Ufybr68eVnVV856RjPBeqbbi1FUjULUTHLp6hD5_4z_1a9Y5dRkyvTgE-OWKlYPgeGbWRepJBiXqmfua8rNSEDpREQRhRG6WDuxZGwu76Aj9tO_KZw9JrNyw9xd9WwjmtRDus_Q7oyXOMLKYMXOtoKC_7aPboBG4nPtP9eNuLBOKblSMMJGMuq_xtPDs-qKE4x36kRO3hnhXh-JEPnw'],
            ['Korean Hydrangea & Peony', 'Buket Tangan', 625000, 'Soft blue hydrangeas with cream garden peony stems.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCkmJZCq62VWeqLGmJ8AawxdbUnyQxeOmLn4_-_3rpRZdfCDSXMQjf2qmQWWmLizVG2P6LoUJ6rSHTZq5b0p-zBG0Snf0nAeNhAssuBA_cSHgPuENJIogMXKY7n5mDdnMbP66MFMZ0kWghj0UdoDuEnKG4FY_l5-fJz6CHbg2bJhW0_q2pf3va1mD0cOK-HpsLR-iD-9qVr4cn2OWW-Afqe1BQw1rqw96MPBiWZ0YU'],
            ['Rustic Preserved Garden Box', 'Preserved & Dried', 485000, 'Dried lavender and preserved blooms in an artisan box.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBdJYA00UasXhv-JPRpdOb3RXfsFZmVgvPisSeqMGw-qZFyClW_IbhS5UprT3OtRKKz_J0cmcjC-vX5YAMlhVkNL__7K8bk5Pu6n8SwoDZIZ2Y7lP7qjbo-gauwbEmWB-NHB8tHb_hFd882gzQMkJzfjx55UY9BK4i_q-fUFFt06F2pVKqcD1PnUTVdTPmbcLUY9WSyrgHvTjhtInN1td4-NUZZMH7BWVmanYg51js'],
            ['Grand Standing Flower', 'Standing Flower', 1250000, 'White lilies and carnations arranged for a memorable opening.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAyTXAqWp-1m6pz334Sy5GZ9tybiuP4obXp-pheMr2t4n6kG23KyZCxK7y_gDt6ssL999XEhoJOs8bpRYvRpSMNr7KnkuXy5znshlLj_Q2rJc9F2va24DCcJ4Wjz84CG4gCbVxgf3KJjse7sZeHu-nTxDWZzjapYoErXeR96GZj0U4PlB4hhNU4XDpNj9rXx7zrWoqID94El7N8EAZMMG0ulIXO0BbIeNELNCXVu2Q'],
        ] as [$name, $category, $price, $description, $image]) {
            Bouquet::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'category' => $category, 'price' => $price, 'description' => $description, 'image_url' => $image]
            );
        }
    }
}
