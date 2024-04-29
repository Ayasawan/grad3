<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Investor;

class investorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('investors')->delete();
        $investors=[
                   [
                       'id'=> '1',
                       'first_name'=> 'shaza',
                       'last_name' =>'asidah',
                       'user_type' =>'investor',
                       'email' =>'shaza@gmail.com',
                       'password'=>bcrypt('3232506'),
                       'phone' =>null,
                       'location' =>null,
                       'iD_card' =>null,
                       'personal_photo' =>null,
                   ],
       
               ];
               Investor::insert($investors);
    }
}
