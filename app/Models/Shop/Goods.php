<?php

namespace App\Models\Shop;

use App\Models\Goods\Vendors;
use Illuminate\Database\Eloquent\Model;

class Goods extends ShopBaseModel
{
    protected $table = 'shop.goods';

    const GOOD_TYPE_LINE     = 'line';
    const GOOD_TYPE_SUB_LINE = 'sub_line';
    const GOOD_TYPE_COMPLEX  = 'complex';

    const PRICE_ROUND_METHOD_DEFAULT = 0;
    const PRICE_ROUND_METHOD_MINUS_1 = 'minus_1';
    const PRICE_ROUND_METHOD_INTEGER = 'integer';

    protected static $fields = [
        'type'                     => [
            'title'   => 'Тип',
            'type'    => parent::FIELD_TYPE_SELECT,
            'options' => [
                self::GOOD_TYPE_LINE     => 'Товар',
                self::GOOD_TYPE_SUB_LINE => 'Компонент',
                self::GOOD_TYPE_COMPLEX  => 'Комплексный товар',
            ],
            'baseField' => true
        ],
        'order_title'              => [
            'title' => 'Короткое наименование',
            'type'  => parent::FIELD_TYPE_STRING,
        ],
        'orderby_manual'           => [
            'title' => 'Ручной прриорите',
            'type'  => parent::FIELD_TYPE_INT,
        ],
        'orderby_auto'             => [
            'title'    => 'Автоприоритет',
            'type'     => parent::FIELD_TYPE_INT,
            'editable' => false,
        ],
        'stop_sale'                => [
            'title' => 'Снято с продажи',
            'type'  => parent::FIELD_TYPE_INT,
        ],
        'cancelled'                => [
            'title' => 'Снято с производства',
            'type'  => parent::FIELD_TYPE_CHECKBOX,
        ],
        'manid'                    => [
            'title'   => 'Производитель',
            'type'    => parent::FIELD_TYPE_SELECT,
            'options' => [],
            'baseField' => true
        ],
        'articul'                  => [
            'title' => 'Артикул',
            'type'  => parent::FIELD_TYPE_STRING,
            'baseField' => true
        ],
        'sarticul'                 => [
            'title' => 'Оптимизированный артикул',
            'type'  => parent::FIELD_TYPE_STRING,
            'baseField' => true
        ],
        'cost'                     => [
            'title' => 'Закупка',
            'type'  => parent::FIELD_TYPE_DOUBLE,
        ],
        'price'                    => [
            'title' => 'Цена',
            'type'  => parent::FIELD_TYPE_DOUBLE,
            'baseField' => true
        ],
        'final_price'              => [
            'title' => 'Цена скидка',
            'type'  => parent::FIELD_TYPE_DOUBLE,
            'baseField' => true
        ],
        'price_opt1'               => [
            'title' => 'ОПТ 1',
            'type'  => parent::FIELD_TYPE_DOUBLE,
        ],
        'price_opt2'               => [
            'title' => 'ОПТ 2',
            'type'  => parent::FIELD_TYPE_DOUBLE,
        ],
        'discount'                 => [
            'title' => 'Скидка',
            'type'  => parent::FIELD_TYPE_DOUBLE,
        ],
        'tarif'                    => [
            'title' => 'Тариф',
            'type'  => parent::FIELD_TYPE_DOUBLE,
        ],
        'tarif_discount'           => [
            'title' => 'Скидка от тарифа',
            'type'  => parent::FIELD_TYPE_DOUBLE,
        ],
        'min_qty'                  => [
            'title' => 'Минимальное количество',
            'type'  => parent::FIELD_TYPE_INT,
        ],
        'ignore_min_qty'           => [
            'title' => 'Игнорировать мин. кол-во',
            'type'  => parent::FIELD_TYPE_CHECKBOX,
        ],
        'weight'                   => [
            'title' => 'Игнорировать мин. кол-во',
            'type'  => parent::FIELD_TYPE_DOUBLE,
        ],
        'final_price_round_method' => [
            'title'   => 'Метод округления цены',
            'type'    => parent::FIELD_TYPE_SELECT,
            'options' => [
                self::PRICE_ROUND_METHOD_DEFAULT => "Оставить как есть",
                self::PRICE_ROUND_METHOD_MINUS_1 => "&gt;1900 — xx90.00, &gt;1000 — xx9.00, &gt;500 — xx9.00&uArr;, &lt;500 — xxx.00",
                self::PRICE_ROUND_METHOD_INTEGER => "До целого",
            ],
        ],
        'nds'                      => [
            'title' => 'НДС',
            'type'  => parent::FIELD_TYPE_DOUBLE,
        ],
        'show_qty'                 => [
            'title' => 'Отображать наличие',
            'type'  => parent::FIELD_TYPE_CHECKBOX,
        ],
        'show_buy'                 => [
            'title'   => 'Кнопка купить',
            'type'    => parent::FIELD_TYPE_SELECT,
            'options' => [
                0 => 'Не задано',
                4 => 'Всегда',
                1 => 'При наличии на нашем складе',
                2 => 'При наличии на любом складе',
                3 => 'Под заказ',
            ],
            'baseField' => true
        ],
        'img_new'                  => [
            'title' => 'Новинка',
            'type'  => parent::FIELD_TYPE_CHECKBOX,
        ],
        'img_promo'                => [
            'title' => 'Акция',
            'type'  => parent::FIELD_TYPE_CHECKBOX,
        ],
        'comments_avg'             => [
            'title' => 'Оценка по отзывам',
            'type'  => parent::FIELD_TYPE_INT,
        ],
        'comments_num'             => [
            'title' => 'Кол-во отзывов',
            'type'  => parent::FIELD_TYPE_INT,
        ],
        'first_inventory'          => [
            'title' => 'Дата первого прихода/переучета',
            'type'  => parent::FIELD_TYPE_DATE,
        ],
        'not_for_ya_market'                => [
            'title' => 'Не показывать в Yandex-market',
            'type'  => parent::FIELD_TYPE_CHECKBOX,
        ],
        'not_for_site_map'                => [
            'title' => 'Не показывать в Sitemap',
            'type'  => parent::FIELD_TYPE_CHECKBOX,
        ],
    ];

    public static function getGoodsTypes()
    {
        return self::$fields['type']['options'];
    }
}
