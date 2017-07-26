<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 24.07.17
 * Time: 18:40
 */

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class ShopBaseModel extends Model
{
    const FIELD_TYPE_INT      = 'int';
    const FIELD_TYPE_STRING   = 'input';
    const FIELD_TYPE_DOUBLE    = 'double';
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
        'parent_id'         => [
            'title' => 'Папка',
            'type'  => self::FIELD_TYPE_INT,
            'baseField' => true
        ],
        'url'=>[
            'title' => 'URL',
            'type'  => self::FIELD_TYPE_STRING,
            'baseField' => true
        ],
        'title'             => [
            'title' => 'Короткое наименование',
            'type'  => self::FIELD_TYPE_STRING,
            'baseField' => true
        ],
        'h1_title'          => [
            'title' => 'H1 наименование',
            'type'  => self::FIELD_TYPE_STRING,
            'baseField' => true
        ],
        'path_title'        => [
            'title' => 'Наименование в пути',
            'type'  => self::FIELD_TYPE_STRING,
        ],
        'orderby'           => [
            'title' => 'Приоритет',
            'type'  => self::FIELD_TYPE_INT,
            'baseField' => true
        ],
        'hidden'            => [
            'title' => 'Скрыт',
            'type'  => self::FIELD_TYPE_CHECKBOX,
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
        'short_description' => [
            'title'       => 'Короткое описание',
            'type'        => self::FIELD_TYPE_TEXT,
            'description' => 'Макс. 255 символов',
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
        $ownFields = [];
        if (isset(get_called_class()::$fields)) {
            $ownFields = get_called_class()::$fields;
        }

        return array_merge(self::$commonFields, $ownFields);
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