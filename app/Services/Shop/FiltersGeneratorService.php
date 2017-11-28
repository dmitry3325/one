<?php

namespace App\Services\Shop;

use App\Models\Shop\EntityFilters;
use App\Models\Shop\Filters;
use App\Models\Shop\Sections;
use App\Models\Shop\Goods;

class FiltersGeneratorService
{

    public function loadFilters($section_id, $entity = null)
    {
        if(!$entity) $entity = Goods::getClassName();
        return EntityFilters::select('shop.entity_filters.*')
            ->join('shop.goods', function ($join) {
                $join->on('shop.goods.id', '=', 'shop.entity_filters.entity_id');
            })
            ->where('shop.goods.section_id', '=', $section_id)
            ->where('shop.entity_filters.entity', '=', $entity)
            ->get();
    }

    public function generateForSection($section_id)
    {
        $section = Sections::findOrFail($section_id);
        $sectionFilters = $section->filters;
        $goodsFL = $this->loadFilters($section_id, Goods::getClassName());
        $filtersFL = $this->loadFilters($section_id, Filters::getClassName());

        $byGood = [];
        foreach($goodsFL as $filter){
            $byGood[$filter->entity_id][$filter->num][] = $filter->value;
        }

        foreach($byGood as $goodFils){
            $list = [];
            foreach($goodFils as $num=>$value){
                $list[$num] = $this->struktura_array($value);
            }
            $list = $this->struktura_array($list);
            dd($list);
        }

    }

    private function struktura_array($mas, $saveKeys = true)
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

    private function walk($good, $arr = [], $filter = 1)
    {
        $r = [];
        foreach ($good as $num => $vals) {
            $n = substr($num, 7);
            if ($n < $filter) continue;
            $filter = $n;
            foreach ($vals as $val) {
                $arr[$num] = $val;
                $filters = $this->walk($good, $arr, $filter + 1);
                if (sizeof($filters) > 0) {
                    foreach ($filters as $fils) {
                        $r[] = array_merge($arr, $fils);
                    }
                } else {
                    $r[] = $arr;
                }
            }
            break;
        }

        return $r;
    }
}