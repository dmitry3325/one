<?php

use App\Models\Shop\Photos;
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

        Photos::truncate();
        Photos::create([
            'entity' => 'App\Models\Shop\Vendors',
            'entity_id' => '1',
            'photo_id' => 12,
            'path' => '/images/test-meal.jpg',
            'hash' => 4343,
        ]);

        Photos::create([
            'entity' => 'App\Models\Shop\Vendors',
            'entity_id' => '2',
            'photo_id' => 10,
            'path' => '/images/duke-farm-logo1.jpg',
            'hash' => 32,
        ]);


    }
}
