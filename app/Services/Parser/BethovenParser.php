<?php

namespace App\Services\Parser;

use App\Models\Photos\Photos;
use App\Models\Shop\Goods;
use App\Models\Shop\Sections;
use App\Models\Shop\Urls;
use App\Services\Common\Images;
use Vendors;

/**
 * Created by PhpStorm.
 * User: dmi
 * Date: 26.01.2018
 * Time: 15:04
 */
class BethovenParser extends ParserAbstractClass
{
    public function parseProduct(): void
    {
        $url   = $this->url;
        $html  = HtmlDomParser::file_get_html($url);
        $props = [];

        //TODO
        //filter generate

        $props['title']    = $html->find('h1', 0)->plaintext;
        $props['h1_title'] = $html->find('h1', 0)->plaintext;
        $props['photos']   = 'http://bethoven.ru/' . $html->find('.img-responsive', 0)->src;

        //удаляем первый абзац с кривой кодировкой
        $html->find('.product-story-info-box', 0)->find('p', 0)->outertext = '';

        $description = $html->find('.product-story-info-box', 0)->innertext();
        $description = ltrim($description);
        $description = rtrim($description);
        $description = iconv('windows-1251', 'UTF-8', $description);

        $props['big_description'] = ($description);

        $parentSectionName = $html->find('.breadcrumb  li a span', 2)->plaintext;
        $parentSection     = Sections::where('title', $parentSectionName)->first();
        if (!$parentSection) {
            $parentSection      = Sections::create(['title' => $parentSectionName, 'h1_title' => $parentSectionName]);
            $parentSection->url = Urls::generateUrlFromText($parentSectionName);
            $parentSection->save();
        }

        $properties = $html->find('.property-table tr');
        foreach ($properties as $property) {
            $name = $property->find('td', 0)->plaintext;
            $name = ltrim(rtrim(str_replace(':', '', $name)));
            $val  = ltrim(rtrim($property->find('td', 1)->plaintext));

            switch ($name) {
                case "Категория":
                    $section = Sections::where('title', $val)->first();
                    if (!$section) {
                        $section      = Sections::create([
                            'title'     => $val,
                            'h1_title'  => $val,
                            'parent_id' => $parentSection->id,
                        ]);
                        $section->url = Urls::generateUrlFromText($val);
                        $section->save();
                    }

                    $props['section_id'] = $section->id;
                    break;
                case "Бренд":
                    $vendor         = Vendors::firstOrCreate(['title' => $val])->first();
                    $props['manid'] = $vendor->id;
                    break;
                default:
                    //                    $props['filters'][$name] = $val;
            }
        }

        $products = $html->find('.productFilter');
        $result   = [];
        foreach ($products as $product) {
            $new = [];

            $articul        = $product->find('.product-code', 0)->plaintext;
            $articul        = str_replace('Артикул: ', '', $articul);
            $new['articul'] = $articul;

            $weight        = $product->find('.weight-on', 0)->plaintext;
            $weight        = floatval(str_replace('кг', '', $weight));
            $new['weight'] = $weight;

            //стандартная цена
            $toRemove = $product->find('.price-standard', 0) ? $product->find('.price-standard', 0)->find('i') : [];
            foreach ($toRemove as $r) {
                $r->innertext = '';
            }
            $standardPrice = $product->find('.price-standard', 0) ? intval($product->find('.price-standard',
                0)->plaintext) : null;

            //цена со скидкой
            $toRemove = $product->find('.price-sales', 0) ? $product->find('.price-sales', 0)->find('i') : [];
            foreach ($toRemove as $r) {
                $r->innertext = '';
            }
            $salePrice = $product->find('.price-sales', 0) ? intval($product->find('.price-sales',
                0)->plaintext) : null;

            //цена со скидкой по карте
            $toRemove = $product->find('.skidkapokarte', 0) ? $product->find('.skidkapokarte', 0)->find('i') : [];
            foreach ($toRemove as $r) {
                $r->innertext = '';
            }
            $skidkapokarte = $product->find('.skidkapokarte', 0) ? intval($product->find('.skidkapokarte',
                0)->plaintext) : null;

            $new['price']       = $standardPrice ?: $salePrice ?: $skidkapokarte ?: 0;
            $new['final_price'] = $salePrice ?: $skidkapokarte ?: $standardPrice ?: 0;

            $new      += $props;
            $result[] = $new;
        }

        $this->products = $result;
    }

    public function saveParsed(): void
    {
        if (empty($this->products)) {
            return;
        }

        for ($i = 0; count($this->products) > $i; $i++) {
            $good        = $this->products[$i];
            $createdGood = Goods::create($good);

            if ($i === 0) {
                $this->parentId    = $createdGood->id;
                $createdGood->type = Goods::GOOD_TYPE_LINE;
            }
            else {
                $createdGood->parent_id = $this->parentId;
                $createdGood->type      = Goods::GOOD_TYPE_SUB_LINE;

            }

            //            $createdGood->url = $i > 0 ? Urls::generateUrlFromText($good['title'] . '_' . $i) : Urls::generateUrlFromText($good['title']);
            $createdGood->save();

            $photos = array_get($good, 'photos');
            if ($photos) {
                Photos::addImg('Goods', $createdGood->id, new Images($photos));
            }
        }

    }

}