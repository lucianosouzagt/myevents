<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BarbecueCategory;
use App\Models\BarbecueItemType;

class BarbecueCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'meat', 'name' => 'Carnes'],
            ['slug' => 'beer', 'name' => 'Cerveja'],
            ['slug' => 'soda', 'name' => 'Refrigerante'],
            ['slug' => 'side', 'name' => 'Acompanhamentos'],
        ];

        $created = [];
        foreach ($categories as $cat) {
            $created[$cat['slug']] = BarbecueCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name']]
            );
        }

        $items = [
            ['slug' => 'meat', 'name' => 'Bovina', 'unit' => 'kg', 'adult' => 0.25, 'child' => 0.12],
            ['slug' => 'meat', 'name' => 'Suína', 'unit' => 'kg', 'adult' => 0.20, 'child' => 0.10],
            ['slug' => 'meat', 'name' => 'Frango', 'unit' => 'kg', 'adult' => 0.18, 'child' => 0.10],
            ['slug' => 'meat', 'name' => 'Linguiça', 'unit' => 'kg', 'adult' => 0.15, 'child' => 0.10],
            ['slug' => 'beer', 'name' => 'Cerveja', 'unit' => 'l', 'adult' => 1.0, 'child' => 0.0],
            ['slug' => 'soda', 'name' => 'Refrigerante', 'unit' => 'l', 'adult' => 0.5, 'child' => 0.5],
            ['slug' => 'side', 'name' => 'Arroz', 'unit' => 'kg', 'adult' => 0.10, 'child' => 0.05],
            ['slug' => 'side', 'name' => 'Vinagrete', 'unit' => 'kg', 'adult' => 0.15, 'child' => 0.08],
            ['slug' => 'side', 'name' => 'Farofa', 'unit' => 'kg', 'adult' => 0.10, 'child' => 0.05],
            ['slug' => 'side', 'name' => 'Maionese', 'unit' => 'kg', 'adult' => 0.05, 'child' => 0.03],
            ['slug' => 'side', 'name' => 'Pão de Alho', 'unit' => 'un', 'adult' => 2, 'child' => 1],
        ];

        foreach ($items as $item) {
            $category = $created[$item['slug']];
            BarbecueItemType::firstOrCreate(
                [
                    'barbecue_category_id' => $category->id,
                    'name' => $item['name'],
                ],
                [
                    'unit' => $item['unit'],
                    'default_per_adult' => $item['adult'],
                    'default_per_child' => $item['child'],
                    'active' => true,
                ]
            );
        }

        // Desativar itens antigos
        BarbecueItemType::whereIn('name', ['Picanha', 'Fraldinha', 'Cerveja Lager', 'Refrigerante Cola'])
            ->update(['active' => false]);
    }
}
