<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class Vendors extends Model
{
    protected $table = 'shop.vendors';

    const FIELD_TYPE_INT      = 'int';
    const FIELD_TYPE_STRING   = 'input';
    const FIELD_TYPE_DOUBLE   = 'double';
    const FIELD_TYPE_TEXT     = 'textarea';
    const FIELD_TYPE_CHECKBOX = 'checkbox';
    const FIELD_TYPE_RADIO    = 'radio';
    const FIELD_TYPE_SELECT   = 'select';
    const FIELD_TYPE_DATE     = 'date';
    const FIELD_TYPE_OBJECT   = 'obejct';

    protected static $commonFields = [
        'id'                => [
            'title'    => 'ID',
            'type'     => self::FIELD_TYPE_INT,
            'editable' => false,
            'baseField' => true
        ],
        'title'             => [
            'title' => 'Короткое наименование',
            'type'  => self::FIELD_TYPE_STRING,
            'baseField' => true
        ],
        'orderby'           => [
            'title' => 'Приоритет',
            'type'  => self::FIELD_TYPE_INT,
            'baseField' => true
        ],
        'picture_id'        => [
            'title' => '№ картинки',
            'type'  => self::FIELD_TYPE_INT,
        ],
        'photos'            => [
            'title'  => 'Изображения',
            'type'   => self::FIELD_TYPE_OBJECT,
            'entity' => 'photos',
        ],
        'created_at'        => [
            'title'    => 'Создан',
            'editable' => false,
            'type'     => self::FIELD_TYPE_DATE,
        ],
        'updated_at'        => [
            'title'    => 'Обновлен',
            'editable' => false,
            'type'     => self::FIELD_TYPE_DATE,
        ],
    ];

    public static function getAllFields()
    {
        return self::$commonFields;
    }

    public static function getBaseFields()
    {
        $fields = [];
        foreach(self::getAllFields() as $k=>$v){
            if(isset($v['baseField']) && $v['baseField']){
                $fields[] = $k;
            }
        }
        return $fields;
    }
}
