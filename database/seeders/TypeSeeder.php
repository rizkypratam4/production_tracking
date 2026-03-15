<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Type::insert([
            [
                'name' => 'SEWA GEDUNG',
                'creator_id' => 2,
                'created_at' => now(),
            ],
            [
                'name' => 'MESIN PRODUKSI',
                'creator_id' => 2,
                'created_at' => now(),
            ],
            [
                'name' => 'FINGERPRINT',
                'creator_id' => 2,
                'created_at' => now(),
                
            ],
            [
                'name' => 'CCTV',
                'creator_id' => 2,
                'created_at' => now(),
            ],
            [
                'name' => 'APAR FOAM',
                'creator_id' => 2,
                'created_at' => now(),
                
            ],
            
        ]);
    }
}
