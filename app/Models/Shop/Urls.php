<?php

namespace App\Models\Shop;

use App\Models\BaseModel;

/**
 * Class Urls
 * @package App\Models\Shop
 */
class Urls extends BaseModel
{
    protected $table = 'shop.urls';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @param $entity
     * @param $entity_id
     * @param $url
     *
     * @return array
     */
    public static function createNew($entity, $entity_id, $url){
        $result = [
            'result' => false
        ];

         if($url && self::where('url', '=', $url)->first()){
             $result['errors'][] = 'Такой URL уже существует на вашем сайте! Адрес должен быть уникален!';
             return $result;
         }

        $result['result'] = self::updateOrCreate([
            'entity' => $entity,
            'entity_id' => $entity_id
        ],[
            'entity' => $entity,
            'entity_id' => $entity_id,
            'url' => $url
        ]);

        return $result;
    }

    /**
     * @param $xxx
     *
     * @return mixed|string
     */
    public static function generateUrlFromText($xxx){
        $xxx = mb_strtolower($xxx) ;
        $sf = array('/ё/','/а/','/б/','/в/','/г/','/д/','/е/','/ж/','/з/','/и/','/й/','/к/','/л/','/м/','/н/','/о/','/п/','/р/','/с/','/т/','/у/','/ф/','/х/','/ц/','/ч/','/ш/','/щ/','/ъ/','/ь/','/ы/','/э/','/ю/','/я/') ;
        $st = array('e','a','b','v','g','d','e','j','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sch','','','y','e','yu','ya') ;
        $xxx = preg_replace($sf,$st,$xxx) ;
        $xxx = preg_replace($sf,$st,$xxx) ;
        $xxx = preg_replace('/[^a-z0-9]+/', '_', $xxx);
        $xxx = preg_replace('/[_]+/', '_', $xxx);
        $xxx = preg_replace('/_\z/', '', $xxx);
        return $xxx ;
    }
}
