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
            if (!isset($existingFiltes[$key])) {
                $toCreate[$key] = $data;
            } else if ($existingFilters[$key]['filter']['hidden']) {
                $toUpdate[$key] = $data;
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
     * @param $section_id
     *
     * @return array
     */
    /*public function fillFilterCombinations($section_id): array
    {
        $startTime = microtime(true);

        $sectionFilters = $this->loadSectionFilters($section_id);
        $filters = $this->getExistingFilters($section_id);

        $_class = Filters::getClassName();

        $All = [];
        foreach ($filters as $key => $data) {
            if (count($data['codes_list']) === 1) {
                foreach ($data['entity_filters'] as $num => $byCode) {
                    if (isset($sectionFilters[$num])) {
                        foreach ($byCode as $code => $eF) {
                            $All[$eF->num]['title'] = $sectionFilters[$num]->value;
                            $All[$eF->num]['list'][$eF->code] = [
                                'value' => $eF->value,
                                'code'  => $eF->code,
                                'url'   => ShopBaseModel::generateUrl($_class, $data['filter']['id'], $data['filter']['url']),
                            ];
                        }
                    }
                }
            }
        }
        /**
         * Получили и сохранили базовую структуру фильтров для раздела

        $this->goodsStorage->setSectionFilters($section_id, 'all_data', $All);

        $Data = [];
        foreach ($filters as $key => $data) {
            $count = count($data['codes_list']);
            foreach ($filters as $key2 => $data2) {
                $count2 = count($data2['codes_list']);
                /**
                 * Проверяем, что фильтр включает все текущие +1
                 * И все фильтры

                if (($count2 - $count) === 1) {
                    $allow = true;
                    foreach ($data['codes_list'] as $fkey) {
                        if (!isset($data2['codes_list'][$fkey])) {
                            $allow = false;
                            break;
                        }
                    }

                    if ($allow) {
                        $filterCode = array_first(array_diff($data2['codes_list'], $data['codes_list']));
                        list($num, $code) = explode('-',$filterCode);
                        $Data[$key][$num][$code] = $data2['filter'];
                    }
                }
            }
        }

        /**
         * Получили и сохранили для выбранных фильтров

        foreach ($Data as $key => $data) {
            $this->goodsStorage->setSectionFilters($section_id, $key, $data);
        }

        return [
            'result' => 'Ok',
            'time'   => microtime(true) - $startTime,
        ];
    }*/
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
     * Загружает существующие фильтры
     *
     * @param $section_id
     */
    private function getExistingFilters($section_id)
    {
        $filterClassName = Filters::getClassName();
        $existingFilters = EntityFilters::select(['shop.entity_filters.*', 'shop.filters.hidden', 'shop.urls.url'])
            ->join('shop.filters', function ($join) {
                $join->on('shop.filters.id', '=', 'shop.entity_filters.entity_id');
            })
            ->join('shop.urls', function ($join) use ($filterClassName) {
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
        $filters = EntityFilters::select('shop.entity_filters.num', 'shop.entity_filters.code', 'shop.entity_filters.value')
            ->join('shop.goods', function ($join) {
                $join->on('shop.goods.id', '=', 'shop.entity_filters.entity_id');
            })
            ->where('shop.goods.section_id', '=', $section_id)
            ->where('shop.goods.hidden', '=', 0)
            ->where('shop.entity_filters.entity', '=', Goods::getClassName())
            ->groupBy('shop.entity_filters.num', 'shop.entity_filters.code')
            ->orderBy(\DB::raw('null'))
            ->get();

        $data = [];
        foreach ($filters as $fil) {
            $data[$fil->num][$fil->code] = [
                'value' => $fil->value,
                'code'  => $fil->code,
            ];
        }

        return $data;
    }

    /**
     * Получаем все товары по фильтрам
     *
     * @param $section_id
     *
     * @return array
     */
    public function getGoodsByFilter($section_id)
    {
        $goodsFLS = EntityFilters::select('shop.entity_filters.*')
            ->join('shop.goods', function ($join) {
                $join->on('shop.goods.id', '=', 'shop.entity_filters.entity_id');
            })
            ->where('shop.goods.section_id', '=', $section_id)
            ->where('shop.goods.hidden', '=', 0)
            ->where('shop.entity_filters.entity', '=', Goods::getClassName())
            ->get();

        $byFilter = [];
        foreach ($goodsFLS as $fl) {
            $byFilter[$fl->num][$fl->code][$fl->entity_id] = $fl->entity_id;
        }

        return $byFilter;
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
            $this->goodsStorage->setFilterGoods($section->id, $key, $data['goods']);

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