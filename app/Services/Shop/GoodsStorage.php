<?php

namespace App\Services\Shop;

use Illuminate\Support\Facades\Redis;

class GoodsStorage
{
    /**
     * Товары по фильтру
     *
     * sectionId -> filterKey -> goodsList
     */
    const FILTERED_GOODS = 'filtered_goods';

    /**
     * Фильтры разделов
     *
     * sectionId => filterKey -> filterData
     *
     */
    const SECTION_FILTERS = 'section_filters';

    protected $sectionFiltersStorage;

    protected $filterGoodsStorage;

    public function __construct()
    {
        $this->sectionFiltersStorage = Redis::connection('section-filters');
        $this->filterGoodsStorage = Redis::connection('filter-goods');
    }

    /**
     * @param int    $section_id
     * @param string $filterKey
     * @param array  $goods
     */
    public function setFilterGoods(int $section_id, string $filterKey, array $goods)
    {
        $this->filterGoodsStorage->hset($section_id, $filterKey, json_encode($goods));
    }

    public function getFilterGoods(int $section_id, string $filterKey): array
    {
        $list = $this->filterGoodsStorage->hget($section_id, $filterKey);
        return json_decode($list, true);
    }
}
