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

    public function addFilterByParams($Q, $filters, $allowedFields = [])
    {
        $methods = self::getFilterMethods();

        $Q->where(function ($q) use ($filters, $methods, $allowedFields) {

            foreach ($filters as $or) {

                $q->orWhere(function ($qq) use ($or, $methods, $allowedFields) {

                    foreach ($or as $and) {
                        if (isset($and['field']) && in_array($and['field'], $allowedFields) &&
                            isset($and['method']) && isset($methods[$and['method']])) {

                            $value = (isset($and['value'])) ? $and['value'] : '';
                            $method = $methods[$and['method']];

                            if($method == 'LIKE') {
                                $qq->where($and['field'], $method, '%'.$value.'%');
                            }else if($method == 'IN'){
                                $qq->whereIn($and['field'], $value);
                            }else{
                                $qq->where($and['field'], $method, $value);
                            }
                        }
                    }
                });
            }
        });
    }
}