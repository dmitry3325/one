<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Integer;

class ShopMetadata extends Model
{
    protected $table = 'shop.shop_metadata';

    protected $guarded = ['id'];

    public static $fields = [
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
    ];

    public static function getAllFields()
    {
        return self::$fields;
    }

    public static function saveMetadata($entity, $id, array $data)
    {
        if (!$entity || !$id || !count($data)) {
            return false;
        }

        foreach ($data as $key => $val) {
            $data = [
                'entity'    => $entity,
                'entity_id' => $id,
                'key'       => $key,
            ];
            self::updateOrCreate($data, array_merge($data, ['value' => $val]));
        }

        return [
            'result' => true,
        ];
    }
}
