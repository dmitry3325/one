<?php

namespace App\Models\Shop;

class Filters extends ShopBaseModel
{
    const COUNT = 8;

    protected $table = 'shop.filters';

    public static function getFieldsTitles()
    {
        $filters = [];
        for ($i = 1; $i <= self::COUNT; $i++) {
            $filters['filter_' . $i]         = [
                'title' => 'Фильтр №' . $i,
                'type'  => parent::FIELD_TYPE_STRING,
            ];
            $filters['filter_' . $i . '_id'] = [
                'title'    => 'Фильтр id №' . $i,
                'type'     => parent::FIELD_TYPE_INT,
                'editable' => false,
            ];
        }

        return array_merge(parent::$commonFields, $filters);
    }
}
