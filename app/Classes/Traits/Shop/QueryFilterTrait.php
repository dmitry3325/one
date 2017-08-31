<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 27.07.17
 * Time: 16:45
 */

namespace App\Classes\Traits\Shop;

trait QueryFilterTrait
{

    public static function getFilterMethods()
    {
        return ['=', '>', '<', '>=', '<=', 'LIKE', 'IN'];
    }

    public static function addFilterByParams($Q, $filters, $allowedFields = [])
    {
        $methods = self::getFilterMethods();
        $table   = self::getTableName();
        $Q->where(function ($q) use ($filters, $methods, $allowedFields, $table) {
            foreach ($filters as $or) {
                $q->orWhere(function ($qq) use ($or, $methods, $allowedFields, $table) {
                    foreach ($or as $and) {
                        if (isset($and['field']) && in_array($and['field'], $allowedFields) &&
                            isset($and['method']) && isset($methods[$and['method']])) {

                            $value  = (isset($and['value'])) ? $and['value'] : '';
                            $method = $methods[$and['method']];

                            if ($method == 'LIKE') {
                                $qq->where($table . '.' . $and['field'], $method, '%' . $value . '%');
                            }
                            else if ($method == 'IN') {
                                $qq->whereIn($table . '.' . $and['field'], $value);
                            }
                            else {
                                $qq->where($table . '.' . $and['field'], $method, $value);
                            }
                        }
                    }
                });
            }
        });
    }

}