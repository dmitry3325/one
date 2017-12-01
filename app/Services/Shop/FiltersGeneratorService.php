<?php

namespace App\Services\Shop;

use App\Models\Shop\EntityFilters;
use App\Models\Shop\Filters;
use App\Models\Shop\Sections;
use App\Models\Shop\Goods;

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
     * @param      $section_id
     * @param null $entity
     *
     * @return mixed
     */
    public function loadFilters($section_id, $entity = null)
    {
        if (!$entity) $entity = Goods::getClassName();

        return EntityFilters::select('shop.entity_filters.*')
            ->join('shop.goods', function ($join) {
                $join->on('shop.goods.id', '=', 'shop.entity_filters.entity_id');
            })
            ->where('shop.goods.section_id', '=', $section_id)
            ->where('shop.entity_filters.entity', '=', $entity)
            ->get();
    }

    /**
     * @param $section_id
     */
    public function generateForSection($section_id)
    {
        $sectionFilters = $this->loadSectionFilters($section_id);
        $goodsFL = $this->loadFilters($section_id, Goods::getClassName());

        $byGood = [];
        foreach ($goodsFL as $filter) {
            if (isset($sectionFilters[$filter->num]) && $sectionFilters[$filter->num]) {
                $byGood[$filter->entity_id][$filter->num . '-' . $filter->code] = $filter;
            }
        }


        $neededFilters = [];
        foreach ($byGood as $id => $goodFils) {
            $arr = $this->generateByFilters($goodFils);
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
                    $list[$filter->entity_id][$filter->num][$filter->code] = $filter;
                }
            }
            sort($key);
            $key = implode('|', $key);
            $Data[$key] = [
                'key' => $key,
                'filters' => $list
            ];
            dump($key);
        }
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

}