<?php

namespace App\Services\Shop;

use App\Models\Shop\EntityFilters;
use App\Models\Shop\Filters;
use App\Models\Shop\Sections;
use App\Models\Shop\Goods;
use App\Models\Shop\ShopBaseModel;
use App\Models\Shop\Urls;


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
     * @var \App\Services\Shop\GoodsStorage
     */
    private $goodsStorage;

    public function __construct(GoodsStorage $goodsStorage)
    {
        $this->goodsStorage = $goodsStorage;
    }

    /**
     * @param $section_id
     */
    public function generateForSection($section_id)
    {
        $section_id
        $Section = Sections::findOrFail($section_id);
        $sectionFilters = $this->loadSectionFilters($section_id);

        $goodsFL = EntityFilters::select('shop.entity_filters.*')
            ->join('shop.goods', function ($join) {
                $join->on('shop.goods.id', '=', 'shop.entity_filters.entity_id');
            })
            ->where('shop.goods.section_id', '=', $section_id)
            ->where('shop.goods.hidden', '=', 0)
            ->where('shop.entity_filters.entity', '=', Goods::getClassName())
            ->get();

        $byGood = [];
        foreach ($goodsFL as $filter) {
            if (isset($sectionFilters[$filter->num]) && $sectionFilters[$filter->num]->auto_create) {
                $byGood[$filter->entity_id][$filter->num . '-' . $filter->code] = $filter;
            }
        }


        $neededFilters = [];
        foreach ($byGood as $id => $goodFils) {
            $filters = $this->generateByFilters($goodFils, $section_id);
            foreach ($filters as $key => $data) {
                if (!isset($neededFilters[$key])) {
                    $neededFilters[$key] = $data;
                }
                $neededFilters[$key]['goods'][] = $id;
            }
        }

        $existingFilters = $this->getExistingFilters($section_id);
        $toCreate = [];
        $toUpdate = [];
        $toHide = [];

        foreach ($neededFilters as $key => $data) {
            if (!isset($existingFilters[$key])) {
                $toCreate[$key] = $data;
            } else if ($existingFilters[$key]['hidden']) {
                $toUpdate[$key] = $existingFilters[$key];
            }
        }

        foreach ($existingFilters as $key => $data) {
            if (!isset($neededFilters[$key])) {
                $toHide[$key] = $data;
            }
        }

        if (count($toCreate) > 0) {
            $this->createFilters($toCreate, $Section);
        }

        if (count($toUpdate) > 0) {
            $ids = [];
            foreach ($toUpdate as $filter) {
                $ids[] = $filter['id'];
            }
            Filters::getDataQuery()->whereIn('id', $ids)->update(['hidden' => 0]);
        }

        if (count($toHide) > 0) {
            $ids = [];
            foreach ($toUpdate as $filter) {
                $ids[] = $filter['id'];
            }
            Filters::getDataQuery()->whereIn('id', $ids)->update(['hidden' => 1]);
        }
    }

    /**
     * @param $section_id
     *
     * @return array
     */
    public function fillFilterCombinations($section_id): array
    {
        $startTime = microtime(true);

        list($schema, $goodsByFilter, $goodsDataById) = $this->getFiltersSchema($section_id);
        $allFilters = $this->getExistingFilters($section_id);


        $this->goodsStorage->setSectionFilters($section_id, $allFilters);
        $this->goodsStorage->setGoodsByFilter($section_id, $goodsByFilter);
        $this->goodsStorage->setFilterSchema($section_id, $schema);
        $this->goodsStorage->setGoodsData($section_id, $goodsDataById);

        return [
            'result' => 'Ok',
            'time'   => microtime(true) - $startTime,
        ];
    }


    /**
     * @param $list
     *
     * @return array
     */
    private function generateByFilters($list)
    {
        $filtersList = $this->strukturaArray($list);
        $Data = [];
        foreach ($filtersList as $byKey) {
            $list = [];
            foreach ($byKey as $k => $filter) {
                if ($filter) {
                    $list[$filter->num][$filter->code] = $filter;
                }
            }
            $key = Filters::getFilterKey($byKey);
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
            $sectionFilters[$f->num] = $f;
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
     * @param $section_id
     *
     * @return array
     */
    private function getExistingFilters($section_id)
    {
        $filterClassName = Filters::getClassName();
        $existingFilters = EntityFilters::select(['shop.entity_filters.*', 'shop.filters.hidden', 'shop.urls.url'])
            ->join('shop.filters', function ($join) {
                $join->on('shop.filters.id', '=', 'shop.entity_filters.entity_id');
            })
            ->leftJoin('shop.urls', function ($join) use ($filterClassName) {
                $join->on('shop.filters.id', '=', 'shop.urls.entity_id');
                $join->where('shop.urls.entity', '=', $filterClassName);
            })
            ->where('shop.filters.section_id', '=', $section_id)
            ->where('shop.entity_filters.entity', '=', $filterClassName)
            ->get();

        $byFilter = [];
        foreach ($existingFilters as $filter) {
            $byFilter[$filter->entity_id][] = $filter;
        }

        $Data = [];
        foreach ($byFilter as $id => $filters) {
            $key = Filters::getFilterKey($filters);
            $eF = array_first($filters);
            if (!isset($Data[$key])) {
                $Data[$key] = [
                    'key'    => $key,
                    'id'     => $id,
                    'hidden' => $eF->hidden,
                    'url'    => ShopBaseModel::getUrl($filterClassName, $id, $eF->url),
                ];
            }
        }

        return $Data;
    }


    /**
     * Получаем схему фильтров для раздела
     *
     * @param $section_id
     *
     * @return array
     */
    public function getFiltersSchema($section_id)
    {
        $filters = EntityFilters::select(
            'shop.entity_filters.num',
            'shop.entity_filters.code',
            'shop.entity_filters.value',
            'shop.entity_filters.entity_id',
            'shop.goods.price',
            'shop.goods.weight')
            ->join('shop.goods', function ($join) {
                $join->on('shop.goods.id', '=', 'shop.entity_filters.entity_id');
            })
            ->where('shop.goods.section_id', '=', $section_id)
            ->where('shop.goods.hidden', '=', 0)
            ->where('shop.entity_filters.entity', '=', Goods::getClassName())
            ->orderBy(\DB::raw('null'))
            ->get();

        $schema = [];
        $goodsByFilter = [];
        $goodsDataById = [];
        $priceRange = $weightRange = [
            'min' => 10000000000000,
            'max' => 0,
        ];

        $Section = Sections::findOrFail($section_id);
        foreach ($Section->filters as $filter) {
            if ($filter->hidden) continue;

            $schema[$filter->num] = [
                'code'     => $filter->code,
                'title'    => $filter->value,
                'order_by' => $filter->order_by,
            ];
            if ($filter->code === EntityFilters::FILTER_PRICE) {
                $schema[$filter->num]['view_type'] = 'data_range';
                $schema[$filter->num]['range'] = [];
            } else if ($filter->code === EntityFilters::FILTER_WEIGHT) {
                $schema[$filter->num]['view_type'] = 'data_range';
                $schema[$filter->num]['range'] = [];
            } else {
                $schema[$filter->num]['view_type'] = 'items_list';
                $schema[$filter->num]['list'] = [];
            }
        }

        foreach ($filters as $fil) {
            if (!isset($schema[$fil->num])) continue;
            if ($schema[$fil->num]['view_type'] === 'items_list') {
                $schema[$fil->num]['list'][$fil->code] = [
                    'value' => $fil->value,
                    'code'  => $fil->code,
                ];
            }

            if ($priceRange['min'] > $fil->price) {
                $priceRange['min'] = $fil->price;
            }
            if ($priceRange['max'] < $fil->price) {
                $priceRange['max'] = $fil->price;
            }

            if ($weightRange['min'] > $fil->weigth) {
                $weightRange['min'] = $fil->weigth;
            }
            if ($weightRange['max'] < $fil->weigth) {
                $weightRange['max'] = $fil->weigth;
            }


            $goodsByFilter[$fil->num][$fil->code][$fil->entity_id] = $fil->entity_id;
            $goodsDataById[$fil->entity_id] = [
                'price'  => $fil->price,
                'weight' => $fil->weight,
            ];
        }

        foreach ($schema as $k => $sh) {
            if ($schema[$k]['code'] === EntityFilters::FILTER_PRICE) {
                $schema[$k]['range'] = $priceRange;
            } else if ($schema[$k]['code'] === EntityFilters::FILTER_WEIGHT) {
                $schema[$k]['range'] = $weightRange;
            } else if ($schema[$k]['view_type'] === 'items_list') {
                if (count($schema[$k]['list']) === 0) {
                    unset($schema[$k]);
                }
            }
        }

        uasort($schema, function ($a, $b) {
            if ($a['order_by'] === $b['order_by']) return 0;

            return (($a['order_by'] < $b['order_by']) ? 1 : -1);
        });


        return [$schema, $goodsByFilter, $goodsDataById];
    }


    /**
     * @param          $Data
     * @param Sections $section
     */
    private function createFilters($Data, Sections $section)
    {
        $entityName = Filters::getClassName();

        $sectionFilters = [];
        foreach ($section->filters as $filter) {
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
                if (isset($sectionFilters[$fNum])) {
                    $forUrl[] = $sectionFilters[$fNum]->value . '_' . implode('_', $values);
                }
            }
            $filter->filters()->createMany($filters);

            $url = Urls::generateUrlFromText(implode(' ', $forUrl));
            if (!Urls::where('url', $url)->first()) {
                $filter->url = $url;
                $filter->save();
            }
        }
    }
}