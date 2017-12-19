<?php

namespace App\Models\Shop;

/**
 * Class HtmlPages
 * @package App\Models\Shop
 */
class HtmlPages extends ShopBaseModel
{
    protected $table = 'shop.html_pages';

    protected $fillable = [
        'title',
        'h1_title',
        'parent_id',
        'orderby',
        'short_description',
        'picture_id',
        'photos',
    ];

    protected static $fields = [
        'id'                => [
            'title'     => 'ID',
            'type'      => ShopBaseModel::FIELD_TYPE_INT,
            'disabled'  => true,
            'baseField' => true,
        ],
        'parent_id'         => [
            'title'     => 'Ид родителя',
            'type'      => ShopBaseModel::FIELD_TYPE_INT,
            'baseField' => true,
        ],
        'title'             => [
            'title'     => 'Короткое наименование',
            'type'      => ShopBaseModel::FIELD_TYPE_STRING,
            'baseField' => true,
        ],
        'h1_title'          => [
            'title'     => 'h1 наименование',
            'type'      => ShopBaseModel::FIELD_TYPE_STRING,
            'baseField' => true,
        ]
        ,
        'short_description' => [
            'title'     => 'Короткое описание',
            'type'      => ShopBaseModel::FIELD_TYPE_STRING,
            'baseField' => true,
        ],
        'picture_id'        => [
            'title'     => '№ картинки',
            'type'      => ShopBaseModel::FIELD_TYPE_INT,
            'baseField' => true,
        ]
    ];

    /**
     * @return array
     */
    public static function getAllFields()
    {
        return self::$fields;
    }

}
