<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class Goods extends ShopBaseModel
{
    protected $table = 'shop.goods';

    const GOOD_TYPE_LINE     = 'line';
    const GOOD_TYPE_SUB_LINE = 'sub_line';
    const GOOD_TYPE_COMPLEX  = 'complex';

    protected static $fields = [
        'type' => [
            'title'   => 'Тип',
            'type'    => self::FIELD_TYPE_SELECT,
            'options' => [
                self::GOOD_TYPE_LINE     => 'Товар',
                self::GOOD_TYPE_SUB_LINE => 'Компонент',
                self::GOOD_TYPE_COMPLEX  => 'Комплексный товар',
            ],
        ],
    ];

    public static function getGoodsTypes()
    {
        return self::$fields['type']['options'];
    }

    public static function getFieldsTitles()
    {
        $filters = [];
        for ($i = 1; $i <= self::COUNT; $i++) {
            $filters['filter_' . $i]         = [
                'title' => 'Фильтр №' . $i,
                'type'  => parent::FIELD_TYPE_INPUT,
            ];
            $filters['filter_' . $i . '_id'] = [
                'title'    => 'Фильтр id №' . $i,
                'type'     => parent::FIELD_TYPE_INT,
                'editable' => false,
            ];
        }

        return array_merge(parent::$commonFields, $filters, self::$fields);
    }
}
