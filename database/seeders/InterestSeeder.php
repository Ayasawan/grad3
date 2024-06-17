<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Interest;
use Illuminate\Support\Facades\DB;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('interests')->delete();
        $interests=[
            [
                'name'=> 'مناسبات شخصية',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name'=> 'حرف يدوية',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name'=> 'مواد غدائية',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name'=> 'تجارة',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name'=> 'تصوير',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name'=> 'مشاريع صغيرة',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name'=> 'فن ',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name'=> 'خدمات مهنية ',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name'=> 'صحة وعناية ',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name'=> 'طبخ',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name'=> 'الزراعة ',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name'=> 'صناعة استهلاكية ',
                'created_at' => now(),
                'updated_at' => now(),

            ]

        ];

        Interest::insert($interests);
    
    }
}
