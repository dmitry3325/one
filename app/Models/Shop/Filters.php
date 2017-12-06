<?php

namespace App\Models\Shop;

/**
 * Class Filters
 * @package App\Models\Shop
 */
class Filters extends ShopBaseModel
{
    protected $table = 'shop.filters';

    /**
     * @param $list
     *
     * @return array|string
     */
    public static function getFilterKey($list)
    {
        $key = [];
        foreach ($list as $eF) {
            if ($eF) {
                $key[] = $eF->num . '-' . $eF->code;
            }
        }
        sort($key);
        $key = implode('|', $key);

        return $key;
    }
}
