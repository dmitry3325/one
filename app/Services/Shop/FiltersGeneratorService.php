<?php

namespace App\Services\Shop;

use App\Models\Shop\EntityFilters;
use App\Models\Shop\Filters;
use App\Models\Shop\Sections;
use App\Models\Shop\Goods;
use App\Models\Shop\Urls;
use Illuminate\Support\Facades\Redis;

/**
 * Сервис создания фильтров.
 *
 * Class FiltersGeneratorService
 * @package App\Services\Shop
 */
class FiltersGeneratorService
{

    /**
     * Тут будем хранить всякое дабы по 100 раз не ходить в базку
     *
     * @var array
     */
    private $storage = [];

    /**
     * @var \Illuminate\Redis\Connections\Connection
     */
    private $sectionFiltersStorage;

    /**
     * @var \Illuminate\Redis\Connections\Connection
     */
    private $filterGoodsStorage;

    public function __construct()
    {
        $this->sectionFiltersStorage = Redis::connection('section-filters');
        $this->filterGoodsStorage = Redis::connection('filter-goods');
    }

    /**
     * @param $section_id
     */
    public function generateForSection($section_id)
    {
        $Section = Sections::findOrFail($section_id);
        $sectionFilters = $this->loadSectionFilters($section_id);
        $goodsFL = EntityFilters::select('shop.entity_filters.*')
            ->join('shop.goods', function ($join) {
                $join->on('shop.goods.id', '=', 'shop.entity_filters.entity_id');
            })
            ->where('shop.goods.section_id', '=', $section_id)
            ->where('shop.entity_filters.entity', '=', Goods::getClassName())
            ->get();

        $byGood = [];
        foreach ($goodsFL as $filter) {
            if (isset($sectionFilters[$filter->num]) && $sectionFilters[$filter->num]) {
                $byGood[$filter->entity_id][$filter->num . '-' . $filter->code] = $filter;
            }
        }


        $neededFilters = [];
        foreach ($byGood as $id => $goodFils) {
            $filters = $this->generateByFilters($goodFils);
            foreach ($filters as $key => $data) {
                if (!isset($neededFilters[$key])) {
                    $neededFilters[$key] = $data;
                }
                $neededFilters[$key]['goods'][] = $id;
            }
        }

        $existingFiltes = $this->getExistingFilters($section_id);
        $toCreate = [];
        $toUpdate = [];
        $toHide = [];

        foreach ($neededFilters as $key => $data) {
            if (!isset($existingFiltes[$key])) {
                $toCreate[$key] = $data;
            } else if (isset($existingFiltes[$key]['filters'][1])) {
                $toUpdate[$key] = $data;
            }
        }

        foreach ($existingFiltes as $key => $data) {
            if (!isset($neededFilters[$key])) {
                $toHide[$key] = $data;
            }
        }

        if (count($toCreate) > 0) {
            $this->createFilters($toCreate, $Section);
        }
    }

    /**
     * @param Goods $good
     */
    public function generateForGood(Goods $good)
    {
        $sectionFilters = $this->loadSectionFilters($good->section_id);
        $goodsFL = EntityFilters::select('shop.entity_filters.*')
            ->where('shop.entity_filters.entity', '=', Goods::getClassName())
            ->where('shop.entity_filters.entity_id', '=', $good->id)
            ->get();

        $grouped = [];
        foreach ($goodsFL as $filter) {
            $grouped[$filter->num . '-' . $filter->code] = $filter;
        }

        $neededFilters = $this->generateByFilters($grouped);

    }

    /**
     * @param $list
     */
    private function generateByFilters($list)
    {
        $filtersList = $this->strukturaArray($list);
        $Data = [];
        foreach ($filtersList as $byKey) {
            $key = [];
            $list = [];
            foreach ($byKey as $k => $filter) {
                if ($filter) {
                    $key[] = $k;
                    $list[$filter->num][$filter->code] = $filter;
                }
            }
            sort($key);
            $key = implode('|', $key);
            $Data[$key] = [
                'key'            => $key,
                'entity_filters' => $list,
            ];
        }

        return $Data;
    }

    /**
     * Загружаем настройки раздела
     *
     * @param      $section_id
     * @param bool $force
     *
     * @return mixed
     */
    private function loadSectionFilters($section_id, $force = false)
    {
        if (isset($this->storage['section_filters'][$section_id]) && !$force) return $this->storage['section_filters'][$section_id];
        $section = Sections::findOrFail($section_id);

        /** У раздела можно включать и выключать формирование фильтра */
        $sectionFilters = [];
        foreach ($section->filters as $f) {
            $sectionFilters[$f->num] = $f->auto_create;
        }

        $this->storage['section_filters'][$section_id] = $sectionFilters;

        return $this->storage['section_filters'][$section_id];
    }

    /**
     * Создает комбинации фильтров
     *
     * @param      $mas
     * @param bool $saveKeys
     *
     * @return array
     */
    private function strukturaArray($mas, $saveKeys = true)
    {
        $keys = array_keys($mas);
        $mas = array_values($mas);
        $col_el = count($mas);
        $col_zn = pow(2, $col_el) - 1;
        $return = [];
        for ($i = 1; $i <= $col_zn; $i++) {
            $dlina_i_bin = decbin($i);
            $zap_str = str_pad($dlina_i_bin, $col_el, "0", STR_PAD_LEFT);
            $zap_dop = strrev($zap_str);
            $dooh = [];
            for ($j = 0; $j < $col_el; $j++) {
                if ($saveKeys) {
                    $dooh[$keys[$j]] = $zap_dop[$j];
                } else $dooh[$j] = $zap_dop[$j];
            }

            $d = 0;
            $a = [];
            foreach ($dooh as $k => $v) {
                if ($v == 1) {
                    $a[$k] = $mas[$d];
                } else {
                    $a[$k] = "";
                }
                $d++;
            }
            $return[] = $a;
        }

        return $return;
    }

    /**
     * Загружает существующие фильтры
     *
     * @param $section_id
     */
    private function getExistingFilters($section_id)
    {
        $existingFilters = EntityFilters::select(['shop.entity_filters.*', 'shop.filters.hidden'])
            ->join('shop.filters', function ($join) {
                $join->on('shop.filters.id', '=', 'shop.entity_filters.entity_id');
            })
            ->where('shop.filters.section_id', '=', $section_id)
            ->where('shop.entity_filters.entity', '=', Filters::getClassName())
            ->get();

        $byFilter = [];
        foreach ($existingFilters as $filter) {
            $byFilter[$filter->entity_id][$filter->num . '-' . $filter->code] = $filter;
        }

        $Data = [];
        foreach ($byFilter as $id => $byCode) {
            $key = [];
            $list = [];
            foreach ($byCode as $code => $eF) {
                $key[] = $code;
                $list[$eF->num][$eF->code] = $eF;
            }
            sort($key);
            $key = implode('|', $key);
            if (!isset($Data[$key])) {
                $Data[$key] = [
                    'key'            => $key,
                    'entity_filters' => $list,
                ];
            }
            $Data[$key]['filters'][$eF->hidden][] = $id;
        }

        return $Data;
    }


    private function createFilters($Data, Sections $section)
    {
        dd($Data);
        $entityName = Filters::getClassName();

        $sectionFilters = [];
        foreach($section->filters as $filter){
            $sectionFilters[$filter->num] = $filter;
        }


        foreach ($Data as $key => $data) {
            $filter = new Filters();
            $filter->section_id = $section->id;
            $filter->save();

            $filters = [];
            $forUrl = [Urls::generateUrlFromText($section->title)];
            foreach ($data['entity_filters'] as $fNum => $filtersList) {
                $values = [];
                foreach ($filtersList as $fil) {
                    $filters[] = [
                        'entity'    => $entityName,
                        'entity_id' => $filter->id,
                        'num'       => $fNum,
                        'value'     => $fil->value,
                        'code'      => $fil->code,
                    ];
                    $values[] = $fil->value;
                }
                if(isset($sectionFilters[$fNum])){
                    $forUrl[] = implode('_',$values);
                }
            }
            $filter->filters()->createMany($filters);

            $url = Urls::generateUrlFromText(implode(' ', $forUrl));
            if(!Urls::where('url', $url)->first()){
                $filter->url = $url;
            }
            dump($key - 'Created!');
            $filter->save();
        }
    }
}