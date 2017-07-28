<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Models\Shop\Vendors;

class BaseShopSeeder extends Seeder
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
        $this->faker = Factory::create();
    }

    /**
     * Run the database seeds.
     */
    public function run()
    {
        Vendors::truncate();

        foreach (range(0, 10) as $i) {
            $user = Vendors::create([
                'title'      => $this->faker->company,
                'orderby'      => $i,
            ]);
            echo ' - ' . $i . ': ' . $user->name . "\n";
        }
    }
}
