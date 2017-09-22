<?php

namespace App\Services\Shop;

use App\Models\Shop\EntityFilters;
use App\Models\Shop\Sections;
use App\Models\Shop\Goods;

class FilterGeneratorService
{
    protected $section;
    protected $filter_values;

    public function __construct($section_id)
    {
        $this->section = Sections::findOrFail($section_id);
    }

    public function loadFilters(){
        $this->filter_values = EntityFilters::select('shop.entity_filters.*')
            ->join('shop.goods', function($join){
                $join->on('shop.goods.id','=','shop.entity_filters.entity_id');
            })
            ->where('shop.goods.section_id','=', $this->section->id)
            ->where('shop.entity_filters.entity','=',Goods::getClassName())
            ->get();

        return $this;
    }

    public function generate(){}
}