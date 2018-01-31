<?php
/**
 * Created by PhpStorm.
 * User: dmi
 * Date: 26.01.2018
 * Time: 16:53
 */

namespace App\Services\Parser;

use Urls;

abstract class ParserAbstractClass
{
    public $url = null;
    public $products;
    public $parentId = null;

    public function __construct(string $url = 'https://www.bethowen.ru/catalogue/dogs/korma/syxoi/korm-dlya-sobak-duke-s-farm-dlya-srednikh-i-krupnykh-porod-indeyka-sukh-2kg/')
    {
        $this->url = $url;
    }

    public abstract function parseProduct(): void;
    public abstract function saveParsed(): void;

    /**
     * проверяет был ли такой урл спаршен
     * @return bool
     */
//    public function checkUrl(): bool
//    {
//        return Urls::where('url', $this->url)->first();
//    }
}