<?php

use App\Models\Photos\Photos;
use App\Models\Shop\EntityFilters;
use App\Models\Shop\ShopMetadata;
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
//        Goods::truncate();
//        Urls::truncate();
//        Photos::truncate();
//        Sections::truncate();
//        ShopMetadata::truncate();
//        EntityFilters::truncate();
        Artisan::call('parseBethoven');
    }
}
