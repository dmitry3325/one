<?php

namespace App\Models\Shop;

use App\Models\BaseModel;

/**
 * Class EntityFilters
 * @package App\Models\Shop
 */
class EntityFilters extends BaseModel
{
    const FILTER_PRICE = 801197857;
    const FILTER_WEIGHT= 782640242;

    protected $table   = 'shop.entity_filters';
    protected $guarded = ['id'];

    /**
     * @param $entity
     * @param $id
     * @param $filter
     * @return bool
     */
    public static function addFilter($entity, $id, $filter)
    {
        if (!ShopBaseModel::checkEntity($entity)) {
            return false;
        }

        if(!isset($filter['value'])) return false;

        return self::create([
            'entity'      => $entity,
            'entity_id'   => $id,
            'num'         => array_get($filter, 'num', 1),
            'value'       => $filter['value'],
            'code'        => self::getCode($filter['value']),
            'auto_create' => array_get($filter, 'auto_create', 0),
            'hidden' => array_get($filter, 'hidden', 0),
            'order_by' => array_get($filter, 'order_by', 0),
        ]);
    }

    /**
     * @param $value
     *
     * @return int
     */
    public static function getCode($value)
    {
        return crc32(mb_strtolower(trim($value)));
    }

    /**
     * @param $entity
     * @param $id
     * @param $value
     *
     * @return bool
     */
    public static function checkExists($entity, $id, $value)
    {
        if (!ShopBaseModel::checkEntity($entity)) {
            return false;
        }

        $filter = EntityFilters::where('entity', $entity)
            ->where('entity_id', $id)
            ->where('code', self::getCode($value))
            ->count();

        return boolval($filter);
    }

    /**
     * @return array
     */
    public static function getDefaultFilters(){
        return [
            self::FILTER_PRICE => 'Цена',
            self::FILTER_WEIGHT => 'Вес'
        ];
    }
}
