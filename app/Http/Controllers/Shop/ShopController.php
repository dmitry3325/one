<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Classes\Traits\Shop\QueryFilterTrait;
use App\Models\Shop\EntityFilters;
use App\Models\Shop\Sections;
use App\Models\Shop\ShopBaseModel;
use App\Models\Shop\Goods;
use App\Models\Shop\Urls;
use App\Services\Shop\FiltersGeneratorService;
use App\Services\Shop\GoodsStorage;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ShopController extends Controller
{
    use QueryFilterTrait;

    public static $appSetts = [
        'title' => 'Товары',
        'js'    => ['apps/shop/goods.js'],
        'css'   => ['apps/shop/goods.css'],
    ];

    /**
     * @param $entity
     * @param array $data
     * @param bool $getEntity
     *
     * @return array
     */
    public function createEntity($entity, $data = [], $getEntity = false)
    {
        $result = [
            'result' => false,
        ];

        if (!ShopBaseModel::checkEntity($entity)) {
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

    /**
     * @param $entity
     * @param $id
     *
     * @return array
     */
    public function deleteEntity($entity, $id)
    {
        if (!ShopBaseModel::checkEntity($entity)) {
            return [];
        }

        $res = $entity::deleteEntity($id);
        return [
            'result' => $res,
        ];
    }

    /**
     * @param $entity
     * @param $id
     * @param $data
     *
     * @return array
     */
    public function updateEntity($entity, $id, $data)
    {
        if (!ShopBaseModel::checkEntity($entity)) {
            return ['result' => false];
        }

        //Фото сохраняются сами
        if(isset($data['photos'])) unset($data['photos']);

        $e = $entity::find($id);
        $e->fill($data);
        $res = $e->save();
        return $res;
    }

    /**
     * @param $entity
     * @param $id
     *
     * @return array
     */
    public function getEntity($entity, $id)
    {
        if (!ShopBaseModel::checkEntity($entity)) {
            return [];
        }
        $table = $entity::getTableName();
        $e     = $entity::getDataQuery()->where($table . '.id', '=', $id)->first();
        if ($e) {
            $e->getMetaData();
            return $e;
        }
        else {
            return [];
        }
    }

    /**
     * @param $entity
     * @param $id
     * @param string $title
     *
     * @return array
     */
    public function generateUrl($entity, $id, $title = '')
    {
        $result = [
            'result' => false,
        ];

        if (!ShopBaseModel::checkEntity($entity)) {
            return $result;
        }

        $e = $entity::find($id);
        if (!$e) {
            return $result;
        }

        $url  = Urls::generateUrlFromText(($title) ? $title : $e->title);
        $find = Urls::where('url', '=', $url)->first();
        if ($find) {
            $result['errors'][] = 'Такой URL уже существует на вашем сайте! Адрес должен быть уникален!';
            return $result;
        }

        $result['result'] = true;
        $result['url']    = $url;

        return $result;
    }

    /**
     * @param $entity
     *
     * @return array
     */
    public function getAllFields($entity)
    {
        if (!ShopBaseModel::checkEntity($entity)) {
            return [];
        }
        return $entity::getAllFields();
    }

    /**
     * @param $entity
     * @param array $options
     *
     * @return array
     */
    public function getItemsList($entity, $options = [])
    {
        $result = [
            'result' => false,
        ];
        if (!ShopBaseModel::checkEntity($entity)) {
            return $result;
        }

        $list = $entity::getData($options);

        if (isset($options['paginate']) && $options['paginate']) {
            return $list;
        }
        else {
            return [
                'data' => $list,
            ];
        }
    }

    /**
     * @param $entity
     *
     * @return array
     */
    public function getBaseFields($entity)
    {
        if (!ShopBaseModel::checkEntity($entity)) {
            return [];
        }
        return $entity::getBaseFields();
    }

    /**
     * @param $entity
     * @param array $options
     *
     * @return array
     */
    public function loadFile($entity, $options = [])
    {
        $result = [
            'result' => false,
        ];
        if (!ShopBaseModel::checkEntity($entity)) {
            return $result;
        }

        $list = $entity::getData($options);

        return Excel::create('file', function ($excel) use ($list) {
            /**@var LaravelExcelWriter $excel */
            $excel->sheet('Sheetname', function ($sheet) use ($list) {
                /**@var LaravelExcelWorksheet $sheet */
                $sheet->disableHeadingGeneration();
                $sheet->with($list);
            });
        })->download('xls');
    }

    /**
     * Фильтры
     */

    /**
     * @param $entity
     * @param $id
     *
     * @return array|\Illuminate\Database\Eloquent\Collection
     */
    public function getEntityFilters($entity, $id)
    {
        $result = [
            'result' => false,
        ];

        if (!ShopBaseModel::checkEntity($entity)) {
            return $result;
        }

        return EntityFilters::where('entity', $entity)->where('entity_id', $id)->get();
    }

    /**
     * @return array
     */
    public function getDefaultEntityFilters(){
        return EntityFilters::getDefaultFilters();
    }

    /**
     * @param $entity
     * @param $id
     * @param $filters
     *
     * @return array|bool|\Illuminate\Database\Eloquent\Model
     */
    public function saveFilters($entity, $id, $filters)
    {
        $result = [
            'result' => false,
        ];

        if (!ShopBaseModel::checkEntity($entity)) {
            return $result;
        }

        EntityFilters::where('entity', $entity)->where('entity_id', $id)->delete();
        foreach ($filters as $filter) {
            if (!array_get($filter, 'value') || !array_get($filter, 'num')) {
                continue;
            }
            EntityFilters::addFilter($entity, $id, $filter);
        }
        $result['result'] = true;
        return $result;
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSectionFilters($id)
    {
        $filters = EntityFilters::where('entity_id', $id)->where('entity', Sections::getClassName())->get()->toArray();

        if (count($filters)) {
            $goodsTable   = Goods::getTableName();
            $filtersTable = EntityFilters::getTableName();
            $values       = EntityFilters::select(\DB::raw('DISTINCT num, value, code'))->join($goodsTable,
                function ($join) use ($goodsTable, $filtersTable) {
                    $join->on($goodsTable . '.id', '=', $filtersTable . '.entity_id');
                    $join->on($filtersTable . '.entity', '=', \DB::raw('"Goods"'));
                })->where('goods.section_id', '=', $id)->orderBy('value', 'asc')->get();

            foreach ($filters as &$filter) {
                foreach ($values as $v) {
                    if ($filter['num'] == $v->num) {
                        $filter['distinct_values'][] = $v;
                    }
                }
            }
        }

        return $filters;
    }


    public function generateFilters($section_id)
    {
        $FilterGenerateService = new FiltersGeneratorService(new GoodsStorage());
        $FilterGenerateService->generateForSection($section_id);
        $FilterGenerateService->fillFilterCombinations($section_id);
    }
}