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
    const PARAM_SEARCH_ENTITY = 'entities';

    private $entities = [
        'Goods',
        'Sections',
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
        }
        $searchList['text'] = $input;
        $vendorQ->where('title', 'LIKE', $searchList['text'] . '%');

        $searchList['mans'] = $vendorQ->pluck('id');

        $result = [];
        foreach ($entities as $entity) {
            if(isset($params['section']) && $params['section'] && $entity === 'Sections'){
                continue;
            }
            if (in_array($entity, $this->entities)) {
                $result[$entity] = $this->makeSearch($entity, $searchList, $params);
            }
        }

        return $result;
    }

    /**
     * @param $entity
     * @param $searchList
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function makeSearch($entity, $searchList, $params = [])
    {
        /** @var \Illuminate\Database\Query\Builder $q */
        $q = $entity::getQuery();
        $q->select($this->getSelectFields($entity));

        if (isset($params['section']) && $params['section'] && $entity !== 'Sections') {
            $q->where('section_id', '=', $params['section']);
        }
        $q->where(function ($q) use ($entity, $searchList) {
            if (isset($searchList['text'])) {
                $text = '%' . $searchList['text'] . '%';
                $q->orWhere('title', 'LIKE', $text);
            }

            if (isset($searchList['id'])) {
                $q->orWhere('id', $searchList['id']);
            }

            if ($entity === 'Goods') {
                if (isset($searchList['text'])) {
                    $text = '%' . $searchList['text'] . '%';
                    $q->orWhere('articul', 'LIKE', $text);
                    $q->orWhere('sarticul', 'LIKE', $text);
                }

                if (isset($searchList['mans']) && count($searchList['mans'])) {
                    $q->orWhere('manid', 'IN', $searchList['mans']);
                }
            }
        });

        $q->orderBy('orderby', 'desc');

        return $q->paginate(20, ['*'], 'page', $params['page'] ?? 1);
    }

    /**
     * @param $entity
     * @return array
     */
    private function getSelectFields($entity)
    {
        $fields = ['id', 'title', 'hidden'];

        switch ($entity) {
            case 'Goods':
                $fields = array_merge($fields, [
                    'manid', 'articul', 'price', 'final_price'
                ]);
                break;
        }

        $fields[] = 'updated_at';

        return $fields;
    }

}