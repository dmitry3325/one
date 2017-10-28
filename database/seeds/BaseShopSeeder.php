<?php

use App\Models\Photos\Photos;
use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Models\Shop\Vendors;

class BaseShopSeeder extends Seeder
{
    /**
     * @var Factory
     */
    private $faker;

    private $names = [
        '1ST CHOICE',
'7 собак',
'8in1',
'Acana',
'Almo Nature',
'Animonda',
'Ankur',
'Api-San',
'Applaws',
'Arden Grange',
'Barking Heads',
'Beaphar',
'Belcando',
'Berkley',
'Best Dinner',
'Bewi Cat',
'Bewi Dog',
'BioMenu',
'Biomill',
'Blitz',
'Bosch',
'Bozita',
'Bozita Super Premium',
'Brava',
'Brit',
'Care Fresh',
'Cat Chow',
'Cat Step',
'Cesar',
'Chappi',
'Chicopee',
'Chomper',
'Cunipic',
'Cunipic',
'DARLING',
'Darsi',
'Daya',
'Delimill',
'Delipet',
'Dezzie',
'Dog Chow',
'Dr. Alderzs',
'Dr. Clauder`s Dreamies',
'Dunya Dogus',
'Edel Cat',
'Edel Dog',
'Eukanuba',
'Ever Clean',
'Farmina',
'Fauna',
'Felix',
'Ferplast',
'Fiory',
'Fish4Dogs',
'Flexi',
'Forzaplast',
'Fresh Step',
'Friskies',
'Gemon',
'Genesis',
'Georplast',
'Gimborn',
'Gimdog',
'Gimpet',
'Gina',
'GO! Natural Holistic',
'Golden Eagle',
'GOURMET',
'Happy Cat',
'Happy Dog',
'Hello Pet',
'Hill`s,Iams',
'JR FARM',
'Karlie',
'Katsu',
'Kitekat',
'Kiti',
'Lechat',
'Leonardo',
'Little One',
'Magnusson',
'MARCHIORO',
'Meowing Heads',
'Mi-Mi',
'Miamor',
'Mimi Litter',
'Molina',
'Mon Ami',
'Monge',
'NERO GOLD',
'№1 Миска',
'№1 Наполнитель',
'№1 Туалет',
'Nobby',
'Nobby',
'Northnate',
'NotSet',
'NOW Fresh',
'Orijen',
'OSSO Fashion',
'Padovan',
'Pedigree',
'Perfect Fit',
'Petreet',
'Pro Pac',
'Pro Pac',
'Pro Plan',
'Prodac',
'Pronature',
'Purina',
'Rinti',
'Rio',
'Rolf Club',
'Royal Canin',
'Sanabelle',
'Sanicat',
'Savic',
'Schesir',
'Schmusy',
'Sheba',
'Simba',
'Special Dog',
'TitBit',
'Totally Ferret',
'Trainer',
'Triol',
'Trixie',
'Ultima',
'Usond',
'Versele-Laga',
'ViPet',
'Vita Pro',
'Vitakraft',
'Wanpy',
'WC For Cats',
'Whiskas',
'ZOOexpress',
'АВЗ',
'Агроветзащита',
'Альпийские луга',
'Вака',
'Велес',
'Гельментал',
'Гестренол',
'Дарелл',
'Деревенские Лакомства',
'Зоогурман',
'ЗООМИР',
'ЗООНИК',
'Зубочистики',
'Каниквантел',
'КонтрСекс',
'Котенок',
'Мнямс',
'Наша Марка',
'Оскар',
'Празител',
'Родные Корма',
'Родные Места',
'Секс-Барьер',
'тест производитель',
'Трапеза',
'Четвероногий Гурман',
    ];

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

        $i = 0;

        foreach (self::$names as $name) {
            $user = Vendors::create([
                'title'      => $name,
                'orderby'      => $i,
            ]);
            $i++;
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
