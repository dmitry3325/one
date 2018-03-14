<?php
/**
 * Created by PhpStorm.
 * User: job
 * Date: 12.03.18
 * Time: 15:54
 */

namespace App\Services\Shop;


use App\Models\Shop\Filters;
use App\Models\Shop\Goods;
use App\Models\Shop\HtmlPages;
use App\Models\Shop\Sections;
use App\Models\Shop\Vendors;
use Doctrine\DBAL\Query\QueryBuilder;

class SearchService
{
    const PARAM_SEARCH_LIMIT = 'limit';
    const PARAM_SEARCH_ENTITY = 'search_entity';

    private $entities = [
        'Sections',
        'Goods',
        'Filters',
        'HtmlPages',
    ];

    /**
     * @param $input
     * @param $params
     *
     * @return array
     */
    public function find($input, $params = [])
    {
        if (strlen($input) < 3) {
            return [];
        }

        $limit = $params[self::PARAM_SEARCH_LIMIT] ?? 0;
        $entities = $params[self::PARAM_SEARCH_ENTITY] ?? $this->entities;

        $searchList = [];
        $vendorQ = Vendors::select('id');
        if (is_numeric($input)) {
            $searchList['id'] = $input;
            $vendorQ->where('id', $searchList['id']);
        } else {
            $searchList['text'] = $input;
            $vendorQ->where('title', 'LIKE', '%'.$searchList['text'].'%');
        }
        $searchList['mans'] = $vendorQ->pluck('id');

        $result = [];
        foreach ($entities as $entity) {
            if (in_array($entity, $this->entities)) {
                $result[$entity] = $this->makeSearch($entity, $searchList, $limit);
            }
        }

        return $result;
    }


    private function makeSearch($entity, $searchList, $limit)
    {
        /** @var \Illuminate\Database\Query\Builder $q */
        $q = $entity::getQuery();

        if(isset($searchList['text'])) {
            $text = '%'.$searchList['text'].'%';
            $q->orWhere('title', 'LIKE', $text);
            $q->orWhere('h1_title', 'LIKE', $text);
        }

        if(isset($searchList['id'])){
            $q->orWhere('id', $searchList['id']);
        }

        if($entity === 'Goods'){
            if(isset($searchList['text'])) {
                $text = '%'.$searchList['text'].'%';
                $q->orWhere('articul', 'LIKE', $text);
                $q->orWhere('sarticul', 'LIKE', $text);
            }

            if(isset($searchList['mans'])) {
                $q->orWhere('manid', 'IN', $searchList['mans']);
            }
        }

        $q->orderBy('orderby', 'desc');
        if($limit) {
            $q->offset(0)->limit($limit);
        }

        return $q->paginate(20);
    }

}