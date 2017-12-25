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
        $this->sectionFiltersStorage = Redis::connection(self::SECTION_FILTERS);
        $this->filterGoodsStorage = Redis::connection(self::FILTERED_GOODS);
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

    /**
     * @param int    $section_id
     * @param string $filterKey
     *
     * @return array
     */
    public function getFilterGoods(int $section_id, string $filterKey = null)
    {
        if ($filterKey) {
            $list = $this->filterGoodsStorage->hget($section_id, $filterKey);

            return ($list) ? json_decode($list, true) : null;
        } else {
            $list = $this->filterGoodsStorage->hgetall($section_id);

            foreach ($list as &$data) {
                $data = json_decode($data, true);
            }

            return $list;
        }
    }

    /**
     * @param int    $section_id
     * @param string $filterKey
     * @param array  $data
     */
    public function setSectionFilters(int $section_id, string $filterKey, array $data)
    {
        $this->sectionFiltersStorage->hset($section_id, $filterKey, json_encode($data));
    }

    /**
     * @param int    $section_id
     * @param string $filterKey
     *
     * @return array
     */
    public function getSectionFilters(int $section_id, string $filterKey = null)
    {
        if ($filterKey) {
            $list = $this->sectionFiltersStorage->hget($section_id, $filterKey);

            return ($list) ? json_decode($list, true) : null;
        } else {
            $list = $this->sectionFiltersStorage->hgetall($section_id);

            foreach ($list as &$data) {
                $data = json_decode($data, true);
            }

            return $list;
        }
    }
}
