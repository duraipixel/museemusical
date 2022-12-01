<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seededAdminEmail = 'adminmusee@yopmail.com';
        $user = User::where('email', '=', $seededAdminEmail)->first();
        if ($user === null) {
            $user = User::create([
                'name'                          => 'Admin',
                'email'                         => $seededAdminEmail,
                'password'                      => Hash::make('password'),
                'is_super_admin'                => 1
            ]);
        }

        // Seed test user 2
        $user = User::where('email', '=', 'userLaravel@yopmail.com')->first();
        if ($user === null) {
            $user = User::create([
                'name'                           => 'User',
                'email'                          => 'user@user.com',
                'password'                       => Hash::make('password'),
            ]);
        }
    }
}
