<?php

namespace App\Models\Shop;

use App\Models\BaseModel;

/**
 * Class EntityFilters
 * @package App\Models\Shop
 */
class EntityFilters extends BaseModel
{
    protected $table   = 'shop.entity_filters';
    protected $guarded = ['id'];

    /**
     * @param $entity
     * @param $id
     * @param $num
     * @param $value
     *
     * @return $this|bool|\Illuminate\Database\Eloquent\Model
     */
    public static function addFilter($entity, $id, $num, $value, $auto_create = 0)
    {
        if (!ShopBaseModel::checkEntity($entity)) {
            return false;
        }

        return self::create([
            'entity'      => $entity,
            'entity_id'   => $id,
            'num'         => $num,
            'value'       => $value,
            'code'        => self::getCode($value),
            'auto_create' => $auto_create,
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
}
