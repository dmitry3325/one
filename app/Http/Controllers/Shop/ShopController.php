<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Filters;
use App\Models\Shop\Goods;
use App\Models\Shop\HtmlPages;
use App\Models\Shop\Sections;
use App\Classes\Traits\Shop\QueryFilterTrait;
use App\Models\Shop\Urls;

class ShopController extends Controller
{
    use QueryFilterTrait;

    public static $appSetts = [
        'title' => 'Товары',
        'js'    => ['apps/shop/goods.js'],
        'css'   => ['apps/shop/goods.css'],
    ];

    private function checkEntity($entity)
    {
        return in_array($entity, ['Sections', 'Filters', 'Goods', 'HtmlPages']);
    }

    public function createEntity($entity, $data = [], $getEntity = false)
    {
        $result = [
            'result' => false,
        ];

        if (!$this->checkEntity($entity)) {
            return $result;
        }

        $e = $entity::create($data);
        if ($e && $e->id) {
            $result = [
                'result' => true,
                'id'     => $e->id,
            ];

            if (count($data)) {
                $e->fill($data);
            }
            $e->save();

            if ($getEntity) {
                $result['entity'] = $entity::find($e->id);
            }
        }

        return $result;
    }

    public function deleteEntity($entity, $id)
    {
        if (!$this->checkEntity($entity)) {
            return [];
        }

        $res = $entity::deleteEntity($id);
        return [
            'result' => $res,
        ];
    }

    public function updateEntity($entity, $id, $data)
    {
        if (!$this->checkEntity($entity)) {
            return ['result' => false];
        }

        $e = $entity::find($id);
        $e->fill($data);
        $res = $e->save();
        return $res;
    }

    public function getEntity($entity, $id)
    {
        if (!$this->checkEntity($entity)) {
            return [];
        }
        $table = $entity::getTableName();
        $e     = $entity::getDataQuery()->where($table . '.id', '=', $id)->first()->getMetaData();
        return $e;
    }

    public function generateUrl($entity, $id)
    {
        $result = [
            'result' => false,
        ];

        if (!$this->checkEntity($entity)) {
            return $result;
        }

        $e = $entity::find($id);
        if (!$e) {
            return $result;
        }

        $url  = Urls::generateUrlFromText($e->title);
        $find = Urls::where('url', '=', $url)->first();
        if ($find) {
            $result['errors'][] = 'Такой URL уже существует на вашем сайте! Адрес должен быть уникален!';
            return $result;
        }

        $result['result'] = true;
        $result['url']    = $url;

        return $result;
    }

    public function getAllFields($entity)
    {
        if (!$this->checkEntity($entity)) {
            return [];
        }
        return $entity::getAllFields();
    }

    public function getItemsList($entity, $options = [])
    {
        $result = [
            'result' => false,
        ];
        if (!$this->checkEntity($entity)) {
            return $result;
        }

        $list = $entity::getData($options);

        if(isset($options['paginate']) && $options['paginate']){
            return $list;
        }else {
            return [
                'data'   => $list,
            ];
        }
    }

    public function getBaseFields($entity)
    {
        if (!$this->checkEntity($entity)) {
            return [];
        }
        return $entity::getBaseFields();
    }
}