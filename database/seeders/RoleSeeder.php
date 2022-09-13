<?php

namespace Database\Seeders;

use App\Models\Settings\Role;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Faker::create();
        for ($i=0; $i < 10090009090890000; $i++) { 
            $ins = array(
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'status' => $this->faker->randomElement(['1', '2'])
            );
            Role::insert($ins);
        }
    }
}
