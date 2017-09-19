<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 24.07.17
 * Time: 18:40
 */

namespace App\Models\Shop;

use App\Classes\Traits\Shop\QueryFilterTrait;
use App\Models\Photos\Photos;
use Illuminate\Pagination\Paginator;
use \App\Models\BaseModel;

/**
 * Class ShopBaseModel
 * @package App\Models\Shop
 */
class ShopBaseModel extends BaseModel
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
    protected        $guarded      = ['id', 'created_at', 'updated_at'];

    /**
     * @param $entity
     *
     * @return bool
     */
    public static function checkEntity($entity)
    {
        return in_array($entity, ['Sections', 'Filters', 'Goods', 'HtmlPages']);
    }

    /**
     * @return array
     */
    public static function getAllFields()
    {
        $ownFields = [];
        $className = self::getClassName(true);
        if (isset($className::$fields)) {
            $ownFields = self::getClassName(true)::$fields;
        }

        if (isset($ownFields['manid']) && !count($ownFields['manid']['options'])) {
            $ownFields['manid']['options'] = Vendors::all()->pluck('title', 'id');
        }

        $metaFields = ShopMetadata::getAllFields();

        return array_merge(self::$commonFields, $ownFields, $metaFields);
    }

    /**
     * @return array
     */
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

    /**
     * Url functions
     */

    /**
     * @return $this
     */
    public function url()
    {
        return $this->hasOne(Urls::class, 'entity_id')->where('entity', '=', self::getClassName());
    }

    /**
     * @return mixed
     */
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

    /**
     * Metadata functions
     */

    /**
     * @return $this
     */
    public function metadata()
    {
        return $this->hasMany(ShopMetadata::class, 'entity_id')->where('entity', '=', self::getClassName());
    }

    /**
     * @param $data
     * @param array $fields
     */
    public static function addMeta(&$data, $fields = [])
    {
        foreach ($data as $entity) {
            $entity->getMetaData($fields);
        }
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function getMetaData($fields = [])
    {
        $q = $this->metadata();
        if (count($fields)) {
            $q->whereIn('key', $fields);
        }
        $meta = $q->get();
        foreach ($meta as $m) {
            $this->{$m->key} = $m->value;
        }
        return $this;
    }

    /**
     * Filters functions
     */

    /**
     * @return $this
     */
    public function filter(){
        return $this->hasMany(ShopMetadata::class, 'entity_id')->where('entity', '=', self::getClassName());
    }

    /**
     * Data functions
     */

    /**
     * @param array $options
     *
     * @return array
     */
    public static function getData($options = [])
    {
        $res = self::getDataQuery($options, true);
        $q   = $res['q'];

        if (isset($options['order_by']['field'])) {
            $type = 'asc';
            if (isset($options['order_by']['type'])) {
                $type = $options['order_by']['type'];
            }
            $q->orderBy($options['order_by']['field'], $type);
        }
        \DB::enableQueryLog();
        $data = [];
        if (isset($options['paginate']) && $options['paginate']) {
            if (isset($options['current_page']) && $options['current_page']) {
                Paginator::currentPageResolver(function () use ($options) {
                    return $options['current_page'];
                });
            }
            $perPage = (isset($options['pages'])) ? $options['pages'] : 50;
            if (isset($options['per_page']) && $options['per_page']) {
                $perPage = $options['per_page'];
            }
            $data = $q->paginate($perPage);
            if (count($res['meta'])) {
                self::addMeta($data, $res['meta']);
            }
        }
        else {
            $data = $q->get();
            if (count($res['meta'])) {
                self::addMeta($data, $res['meta']);
            }
        }
        //dd(\DB::getQueryLog());
        return $data;
    }

    /**
     * @param array $options
     * @param bool $getMetaFields
     *
     * @return array|\Illuminate\Database\Query\Builder|static
     */
    public static function getDataQuery($options = [], $getMetaFields = false)
    {
        $table  = self::getTableName();
        $entity = self::getClassName();

        $metaData = [];
        $select   = [$table . '.*', 'urls.url'];
        if (array_get($options, 'fields')) {
            $select = [];
            $metaFields = ShopMetadata::getAllFields();
            foreach ($options['fields'] as $fld) {
                if ($fld === 'url') {
                    $select[] = 'urls.url';
                }
                else if (isset($metaFields[$fld])) {
                    $metaData[] = $fld;
                }
                else {
                    $select[] = $table . '.' . $fld;
                }
            }
        }
        $q = self::select($select)
            ->leftJoin('shop.urls', function ($on) use ($entity, $table) {
                $on->where('entity', '=', $entity);
                $on->where('entity_id', '=', \DB::raw($table . '.id'));
            });

        if (array_get($options, 'section_id')) {
            $q->where($table . '.section_id', '=', $options['section_id']);
        }

        if (array_get($options, 'filters')) {
            $allFields = array_keys(self::getAllFields($entity));
            self::addFilterByParams($q, $options['filters']);
        }

        if ($getMetaFields) {
            return [
                'q'    => $q,
                'meta' => $metaData,
            ];
        }
        else {
            return $q;
        }
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function save(array $options = [])
    {
        $metaDataFields = ShopMetadata::getAllFields();

        $result = [
            'result' => false,
        ];

        $setToMeta = [];
        $dirty     = $this->getDirty();
        foreach ($metaDataFields as $fld => $val) {
            if (isset($this->attributes[$fld]) && isset($dirty[$fld])) {
                $setToMeta[$fld] = $this->attributes[$fld];
                unset($this->attributes[$fld]);
            }
        }

        $setUrl = [];
        if (isset($dirty['url'])) {
            $res = Urls::createNew(self::getClassName(), $this->id, $this->attributes['url']);
            if (isset($res['errors'])) {
                $result['errors'] = $res['errors'];
            }
            $setUrl['url'] = $this->attributes['url'];
            unset($this->attributes['url']);
        }

        $result['result'] = parent::save($options);

        if (count($setToMeta)) {
            $res = ShopMetadata::saveMetadata(self::getClassName(), $this->id, $setToMeta);
            if (isset($res['errors'])) {
                $result['errors'] = $res['errors'];
            }
        }

        $this->attributes = array_merge($this->attributes, $setToMeta, $setUrl);

        return $result;
    }

    /**
     * @param $id
     *
     * @return bool|null
     */
    public static function deleteEntity($id)
    {
        $entity = self::getClassName();
        Urls::where('entity', '=', $entity)->where('id', '=', $id)->delete();
        ShopMetadata::where('entity', '=', $entity)->where('id', '=', $id)->delete();
        return self::where('id', '=', $id)->delete();
    }

    /**
     * Photos functions
     */

    /**
     * @return array
     */
    public function savePhotos()
    {
        $list = $this->photos()->get();

        $photos = [];
        foreach ($list as $l) {
            if ($l->hidden) {
                continue;
            }
            $photos[] = $l->photo_id;
        }

        $this->photos = json_encode($photos);
        $this->save();

        return $photos;
    }

    /**
     * @return array
     */
    public function setPhotosAttribute(){
        $this->savePhotos();
    }

    /**
     * @return $this
     */
    public function photos()
    {
        return $this->hasMany(Photos::class, 'entity_id')->where('entity', '=', self::getClassName());
    }

    /**
     * @return array
     */
    public function getPhotosAttribute()
    {
        return $this->getPhotos('jpeg');
    }

    /**
     * @param string $ext
     *
     * @return array
     */
    public function getPhotos($ext = 'jpeg')
    {
        $ph     = ($this->attributes['photos']) ? json_decode($this->attributes['photos'], true) : [];
        $photos = [];
        foreach (Photos::$sizes as $size => $photo) {
            foreach ($ph as $num) {
                $this->getPhotoUrl($size, $num, $ext);
            }
        }
        return $photos;
    }

    /**
     * @param $size
     * @param int $num
     * @param string $ext
     *
     * @return string
     */
    public function getPhotoUrl($size, $num = 1, $ext = 'jpg')
    {
        if ($this->url) {
            return Photos::PIC_PATH . '/' . $size . '/' . $this->attributes['url'] . (($num !== 1) ? '_' . $num : '') . '.' . $ext;
        }
        else {
            return Photos::PIC_PATH . '/' . $size . '/' . self::getTableName(true) . '/' . $this->id . (($num !== 1) ? '_' . $num : '') . '.' . $ext;
        }
    }
}