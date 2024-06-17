<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Type;
use Illuminate\Support\Facades\DB;


class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('types')->delete();
        $types=[
            [
                'name'=> 'إبداعي',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name'=> 'صناعي',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name'=> 'تجاري',
                'created_at' => now(),
                'updated_at' => now(),

            ]

        ];

        Type::insert($types);
    
    }
}
