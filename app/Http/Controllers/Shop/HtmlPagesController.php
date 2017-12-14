<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Response\Success;
use App\Models\Shop\HtmlPages;
use App\Models\Shop\Vendors;

/**
 * Class VendorsController
 * @package App\Http\Controllers\Shop
 */
class HtmlPagesController extends Controller
{

    public static $appSetts = [
        'title' => 'Хтмл страницы',
        'js'    => ['apps/shop/htmlPages.js'],
    ];

    public function getAllFields()
    {
        $fields = HtmlPages::getAllFields();

        return new Success(null, $fields);
    }

    public function getHtmlPagesList($filters = [], $fields = [])
    {
        $q = HtmlPages::query()->orderBy('id', 'DESC');

        return new Success(null, $q->get());
    }

    public function delete($id)
    {
        HtmlPages::find($id)->delete();

        return new Success();
    }

    /**
     * @return array
     */
    public function create()
    {
        $htmlPage = new HtmlPages();
        $htmlPage->save();

        return new Success(null, $htmlPage);

    }

    /**
     * @param $id
     * @param $data
     *
     */
    public function update($id, $data)
    {
        $htmlPage = HtmlPages::find($id);

        foreach ($data as $key => $value) {
            $htmlPage->$key = $value;
        }
        $htmlPage->save();

        return new Success();

    }

    public function saveHtml($id, $content)
    {
        $htmlPage       = HtmlPages::find($id);
        $htmlPage->body = $content;
        $htmlPage->save();

        return new Success();
    }

    public function getHtmlMeta($id)
    {
        $htmlPage = HtmlPages::with('metadata')->where('id', $id)->first();

        return new Success(null, $htmlPage->metadata);
    }

}