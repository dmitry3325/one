<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 27.07.17
 * Time: 16:45
 */

namespace App\Classes\Traits\Shop;


use App\Models\Shop\Urls;

trait QueryFilterTrait
{

    /**
     * @return array
     */
    public static function getFilterMethods()
    {
        return ['=','!=', '>', '<', '>=', '<=', 'LIKE', 'IN'];
    }

    /**
     * @param $Q
     * @param $filters
     * @param array $allowedFields
     */
    public static function addFilterByParams($Q, $filters, $allowedFields = [])
    {
        $methods = self::getFilterMethods();
        $mainTable   = self::getTableName();
        $urlTable = with(new Urls)->getTable();
        $Q->where(function ($q) use ($filters, $methods, $allowedFields, $mainTable, $urlTable) {
            foreach ($filters as $or) {
                $q->orWhere(function ($qq) use ($or, $methods, $allowedFields, $mainTable, $urlTable) {
                    foreach ($or as $and) {
                        if (isset($and['field']) && (!count($allowedFields) || in_array($and['field'], $allowedFields)) &&
                            isset($and['method']) && isset($methods[$and['method']])) {

                            $table = $mainTable;
                            if($and['field'] == 'url'){
                                $table = $urlTable;
                            }

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