<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 24.07.17
 * Time: 18:40
 */

namespace App\Models\Shop;

use App\Classes\Traits\Shop\QueryFilterTrait;
use Illuminate\Database\Eloquent\Model;

class ShopBaseModel extends Model
{
    use QueryFilterTrait;

    const FIELD_TYPE_INT      = 'int';
    const FIELD_TYPE_STRING   = 'input';
    const FIELD_TYPE_DOUBLE   = 'double';
    const FIELD_TYPE_TEXT     = 'textarea';
    const FIELD_TYPE_CHECKBOX = 'checkbox';
    const FIELD_TYPE_RADIO    = 'radio';
    const FIELD_TYPE_SELECT   = 'select';
    const FIELD_TYPE_DATE     = 'date';
    const FIELD_TYPE_OBJECT   = 'obejct';

    protected static $class_name;
    protected static $class_full_name;
    protected static $fields;
    protected static $commonFields = [
        'id'                => [
            'title'     => 'ID',
            'type'      => self::FIELD_TYPE_INT,
            'editable'  => false,
            'baseField' => true,
        ],
        'parent_id'         => [
            'title'     => 'Родитель',
            'type'      => self::FIELD_TYPE_INT,
            'baseField' => true,
        ],
        'url'               => [
            'title'     => 'URL',
            'type'      => self::FIELD_TYPE_STRING,
            'baseField' => true,
        ],
        'title'             => [
            'title'     => 'Короткое наименование',
            'type'      => self::FIELD_TYPE_STRING,
            'baseField' => true,
        ],
        'h1_title'          => [
            'title'     => 'H1 наименование',
            'type'      => self::FIELD_TYPE_STRING,
            'baseField' => true,
        ],
        'path_title'        => [
            'title' => 'Наименование в пути',
            'type'  => self::FIELD_TYPE_STRING,
        ],
        'orderby'           => [
            'title'     => 'Приоритет',
            'type'      => self::FIELD_TYPE_INT,
            'baseField' => true,
        ],
        'hidden'            => [
            'title'     => 'Скрыт',
            'type'      => self::FIELD_TYPE_CHECKBOX,
            'baseField' => true,
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
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static function getAllFields()
    {
        $ownFields = [];
        $className = self::getClassName(true);
        if (isset($className::$fields)) {
            $ownFields = self::getClassName(true)::$fields;
        }

        $filters = [];
        if($className!='HtmlPages') {
            for ($i = 1; $i <= Filters::COUNT; $i++) {
                $filters['filter_' . $i]         = [
                    'title' => 'Фильтр №' . $i,
                    'type'  => self::FIELD_TYPE_STRING,
                ];
                $filters['filter_' . $i . '_id'] = [
                    'title'    => 'Фильтр id №' . $i,
                    'type'     => self::FIELD_TYPE_INT,
                    'editable' => false,
                ];
            }
        }

        $metaFields = ShopMetadata::getAllFields();

        return array_merge(self::$commonFields, $filters, $ownFields, $metaFields);
    }

    public static function getBaseFields()
    {
        $fields = [];
        foreach (self::getAllFields() as $k => $v) {
            if (isset($v['baseField']) && $v['baseField']) {
                $fields[] = $k;
            }
        }
        return $fields;
    }

    public function url()
    {
        return $this->hasOne(Urls::class, 'entity_id')->where('entity', '=', self::getClassName());
    }

    public function metadata()
    {
        return $this->hasMany(ShopMetadata::class, 'entity_id')->where('entity', '=', self::getClassName());
    }

    public function getUrlAttribute()
    {
        if (isset($this->attributes['url'])) {
            return $this->attributes['url'];
        }
        else {
            if (isset($this->relations['url']->id)) {
                $this->attributes['url'] = $this->relations['url']->url;
            }
            else {
                $url                     = $this->url()->first();
                $this->attributes['url'] = ($url) ? $url->url : '';
            }
            return $this->attributes['url'];
        }
    }

    public function save(array $options = []){
        $metaDataFields = ShopMetadata::getAllFields();

        $result = [
            'result' => false
        ];

        $setToMeta = [];
        $dirty = $this->getDirty();
        foreach($metaDataFields as $fld=>$val){
            if(isset($this->attributes[$fld]) && isset($dirty[$fld])){
                $setToMeta[$fld] = $this->attributes[$fld];
                unset($this->attributes[$fld]);
            }
        }

        $setUrl = [];
        if(isset($dirty['url'])){
            $res = Urls::createNew(self::getClassName(), $this->id, $this->attributes['url']);
            if(isset($res['errors'])){
                $result['errors'] = $res['errors'];
            }
            $setUrl['url'] = $this->attributes['url'];
            unset($this->attributes['url']);
        }

        $result['result'] = parent::save($options);

        if(count($setToMeta)){
            $res = ShopMetadata::saveMetadata(self::getClassName(), $this->id, $setToMeta);
            if(isset($res['errors'])){
                $result['errors'] = $res['errors'];
            }
        }

        $this->attributes = array_merge($this->attributes, $setToMeta, $setUrl);

        return $result;
    }

    public static function getClassName($full = false)
    {
        if (self::$class_name && !$full) {
            return self::$class_name;
        }
        if (self::$class_full_name && $full) {
            return self::$class_full_name;
        }

        self::$class_full_name = get_called_class();
        $arr                   = explode('\\', self::$class_full_name);
        self::$class_name      = end($arr);
        return ($full) ? self::$class_full_name : self::$class_name;
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function getData($options = [])
    {
         return self::getDataQuery($options)->get();
    }

    public static function getDataQuery($options = [])
    {
        $table  = self::getTableName();
        $entity = self::getClassName(true);
        $q      = self::select([$table . '.*', 'urls.url'])
            ->leftJoin('shop.urls', function ($on) use ($entity, $table) {
                $on->where('entity', '=', $entity);
                $on->where('entity_id', '=', $table . '.id');
            });

        if(isset($options['section_id']) && $options['section_id']){
            $q->where($table.'.section_id', '=', $options['section_id']);
        }

        if (isset($options['filters']) && count($options['filters'])) {
            $allFields = array_keys(self::getAllFields($entity));
            self::addFilterByParams($q, $options['filters'], $allFields);
        }

        return $q;
    }

    public static function deleteEntity($id){
        $entity = self::getClassName();
        Urls::where('entity','=',$entity)->where('id','=',$id)->delete();
        ShopMetadata::where('entity','=',$entity)->where('id','=',$id)->delete();
        return self::where('id','=', $id)->delete();
    }

    public function getMetaData(){
        $meta = $this->metadata()->get();
         foreach($meta as $m){
            $this->{$m->key} = $m->value;
        }
        return $this;
    }
}