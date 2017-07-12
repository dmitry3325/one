<?php

use App\Models\Auth\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * @var Factory
     */
    private $faker;

    /**
     * ArticlesSeeder constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create('ru_RU');
    }

    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::truncate();

        User::create([
            'firstname'    => 'Admin',
            'lastname'     => 'Admin',
            'email'        => 'admin@admin.com',
            'password'     => 'admin',
            'is_confirmed' => true,
        ]);

        foreach (range(0, 10) as $i) {
            $user = User::create([
                'firstname'    => $this->faker->firstName,
                'lastname'     => $this->faker->lastName,
                'email'        => $this->faker->email,
                'password'     => md5((string)random_int(0, PHP_INT_MAX)),
                'is_confirmed' => true,
            ]);
            echo ' - ' . $i . ': ' . $user->name . "\n";
        }
    }
}
