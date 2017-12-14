<?php

namespace App\Models\Shop;

use App\Models\BaseModel;

/**
 * Class ShopMetadata
 * @package App\Models\Shop
 */
class ShopMetadata extends BaseModel
{
    protected $table = 'shop.shop_metadata';

    protected $guarded = ['id'];

    protected static $fields = [
        'html_title'       => [
            'title' => 'HTML title',
            'type'  => ShopBaseModel::FIELD_TYPE_STRING,
        ],
        'html_keywords'    => [
            'title' => 'KEYWORDS',
            'type'  => ShopBaseModel::FIELD_TYPE_TEXT,
        ],
        'html_description' => [
            'title' => 'DESCRIPTION',
            'type'  => ShopBaseModel::FIELD_TYPE_TEXT,
        ],
        'big_description' => [
            'title' => 'Полное описание',
            'type'  => ShopBaseModel::FIELD_TYPE_TEXT,
        ],
        'goods_links_data' => [
            'title' => 'Список связаных товаров',
            'type'  => ShopBaseModel::FIELD_TYPE_TEXT,
        ],
        'components' => [
            'title' => 'Компоненты товара',
            'type'  => ShopBaseModel::FIELD_TYPE_STRING,
        ],
        'body' => [
            'title' => 'Body',
            'type'  => ShopBaseModel::FIELD_TYPE_TEXT,
        ],
    ];

    /**
     * @return array
     */
    public static function getAllFields()
    {
        /*for($i = 1; $i<=Filters::COUNT; $i++){
            self::$fields['filter_'.$i.'_use_in_autocreate'] = [
                'title' => 'Создавать автоматически',
                'type'  => ShopBaseModel::FIELD_TYPE_CHECKBOX,
            ];
        }*/
        return self::$fields;
    }

    /**
     * @param $entity
     * @param $id
     * @param array $data
     *
     * @return array|bool
     */
    public static function saveMetadata($entity, $id, array $data)
    {
        if (!$entity || !$id || !count($data)) {
            return [
                'result' => false,
            ];
        }

        foreach ($data as $key => $val) {
            $data = [
                'entity'    => $entity,
                'entity_id' => $id,
                'key'       => $key,
            ];
            if(is_array($val)){
                $val = json_encode($val);
            }
            self::updateOrCreate($data, array_merge($data, ['value' => $val]));
        }

        return [
            'result' => true,
        ];
    }
}
