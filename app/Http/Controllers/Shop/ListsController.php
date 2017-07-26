<?php

namespace App\Http\Controllers\Shop;

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

    public function getAllFields($entity)
    {
        switch ($entity) {
            case 'sections':
                return Sections::getAllFields();
                break;
            case 'filters':
                return Filters::getAllFields();
                break;
            case 'goods':
                return Goods::getAllFields();
                break;
            case 'html_pages':
                return HtmlPages::getAllFields();
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
            'list'   => $q->get(),
        ];
    }

    public function getBaseFields($entity)
    {
        switch ($entity) {
            case 'sections':
                return Sections::getBaseFields();
                break;
            case 'filters':
                return Filters::getBaseFields();
                break;
            case 'goods':
                return Goods::getBaseFields();
                break;
            case 'html_pages':
                return HtmlPages::getBaseFields();
                break;
        }
        return [];
    }
}