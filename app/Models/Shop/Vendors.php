<?php

namespace App\Models\Shop;

use App\Models\BaseModel;

/**
 * Class Vendors
 * @package App\Models\Shop
 */
class Vendors extends BaseModel
{
    protected $table = 'shop.vendors';

    protected $fillable = ['title', 'orderby', 'picture_id', 'photos'];

    protected static $commonFields = [
        'id'                => [
            'title'    => 'ID',
            'type'     => ShopBaseModel::FIELD_TYPE_INT,
            'disabled' => true,
            'baseField' => true
        ],
        'title'             => [
            'title' => 'Короткое наименование',
            'type'  => ShopBaseModel::FIELD_TYPE_STRING,
            'baseField' => true
        ],
        'orderby'           => [
            'title' => 'Приоритет',
            'type'  => ShopBaseModel::FIELD_TYPE_INT,
            'baseField' => true
        ],
        'picture_id'        => [
            'title' => '№ картинки',
            'type'  => ShopBaseModel::FIELD_TYPE_INT,
            'baseField' => true
        ],
        'photos'            => [
            'title'  => 'Изображения',
            'type'   => ShopBaseModel::FIELD_TYPE_OBJECT,
            'entity' => 'photos',
            'baseField' => true
        ]
    ];

    /**
     * @return array
     */
    public static function getAllFields()
    {
        return self::$commonFields;
    }

}
