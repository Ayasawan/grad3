<?php
namespace Database\Seeders;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->delete();
        $admins=[
            [
                'first_name'=> 'abeer',
                'last_name'=>'sy',
                'user_type' =>'admin',
                'email' =>'abee@r.com',
                'password'=>bcrypt('123123123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name'=> 'aya',
                'last_name'=>'sy',
                'user_type' =>'admin',
                'email' =>'aya@google.com',
                'password'=>bcrypt('0932'),
                'created_at' => now(),
                'updated_at' => now(),

            ]
        ];
        Admin::insert($admins);
    }
}
