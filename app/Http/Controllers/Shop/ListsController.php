<?php

namespace App\Http\Controllers\Shop;

use App\Classes\Packages\LinePackage;
use App\Http\Controllers\Controller;
use App\Models\Shop\Filters;
use App\Models\Shop\Goods;
use App\Models\Shop\HtmlPages;
use App\Models\Shop\Sections;

class ListsController extends Controller
{

    public static $appSetts = [
        'title' => 'Сайт',
        'js'    => ['apps/shop/itemsList.js'],
        'css'   => ['apps/shop/itemsList.css'],
    ];

    public function createEntity($type)
    {
        $entity = null;
        switch ($type) {
            case 'sections':
                $entity = Sections::create();
                break;
            case 'filters':
                $entity = Filters::create();
                break;
            case 'goods':
                $entity = Goods::create();
                break;
            case 'html_pages':
                $entity = HtmlPages::create();
                break;
        }

        if ($entity && $entity->id) {
            return [
                'result' => true,
                'id'     => $entity->id,
            ];
        }
        else {
            return [
                'result' => false,
            ];
        }
    }

    public function getFieldsTitles($entity)
    {
        switch ($entity) {
            case 'sections':
                return Sections::getFieldsTitles();
                break;
            case 'filters':
                return Filters::getFieldsTitles();
                break;
            case 'goods':
                return Goods::getFieldsTitles();
                break;
            case 'html_pages':
                return HtmlPages::getFieldsTitles();
                break;
        }
        return [];
    }

    public function getItemsList($entity, $filters = [], $fields = [])
    {
        $q = null;
        switch ($entity) {
            case 'sections':
                $q = Sections::query();
                break;
            case 'filters':
                $q = Filters::query();
                break;
            case 'goods':
                $q = Goods::query();
                break;
            case 'html_pages':
                $q = HtmlPages::query();
                break;
        }

        return [
            'result' => true,
            'lists'  => $q->get(),
        ];
    }
}