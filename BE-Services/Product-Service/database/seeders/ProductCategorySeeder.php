<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Product\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('product_categories')->insertOrIgnore([
            [
                'id'            => 1,
                'category_name' => 'Serum',
                'deleted_at'    => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 2,
                'category_name' => 'Moisturiser',
                'deleted_at'    => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 3,
                'category_name' => 'Sunscreen',
                'deleted_at'    => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 4,
                'category_name' => 'Treatment',
                'deleted_at'    => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 5,
                'category_name' => 'Cleanser',
                'deleted_at'    => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ]);
    }
}