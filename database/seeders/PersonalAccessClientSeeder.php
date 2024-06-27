<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;


use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

class PersonalAccessClientSeeder extends Seeder
{
    public function run()
    {
        $clientRepository = new ClientRepository();

        // $host = $_SERVER['HTTP_HOST'];
        $host = Request::getHost();

        $redirectUri = "http://' . $host";

        $client = $clientRepository->createPersonalAccessClient(
            null, // no user ID, since it's a personal access client
            'Personal Access Client', // client name
            $redirectUri // redirect URI
        );

        echo "Personal access client created with ID: {$client->id} and secret: {$client->secret}\n";
    }
}