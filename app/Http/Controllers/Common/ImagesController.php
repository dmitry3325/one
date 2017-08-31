<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Photos\Photos;
use App\Models\Shop\ShopBaseModel;
use App\Models\Shop\Urls;
use App\Services\Common\Images;
use Faker\Provider\Image;

class ImagesController extends Controller
{
    public function index()
    {
        $Info = $this->getImageInfo();
        if (!isset($Info['entity']) || !isset($Info['entity_id']) || !ShopBaseModel::checkEntity($Info['entity'])) {
            return $this->page_404();
        }

        $photo = Photos::where('entity', '=', $Info['entity'])
            ->where('entity_id', '=', $Info['entity_id'])
            ->where('photo_id', '=', $Info['photo_id'])->first();


        if(!$photo || !file_exists($photo->path) || !isset(Photos::$sizes[$Info['size']])) return $this->page_404();

        $img = new Images();
        $img->fromFile($photo->path);
        $size = Photos::$sizes[$Info['size']];
        $img->resize($size['width'], null);

        $path = str_replace(Photos::PIC_PATH,'','/'.\Request::path());

        Photos::saveFile($img, $path, Photos::getBasePublicPath());
        return $img->toScreen();
    }

    private function getImageInfo()
    {
        $Data = [];
        $path = \Request::path();
        preg_match('#p\/(.+)\.(gif|jpg|jpeg|png)\z#', $path, $matches);
        if (count($matches) != 3) {
            return false;
        }
        $Data['filetype'] = $matches[2];

        $fn = function ($url) {
            $split  = explode('_', $url);
            $last   = end($split);
            $number = 1; // По умолчанию 1 картинка
            if (is_numeric($last) && count($split) > 1) {
                $number = $last;
                $url    = substr($url, 0, strlen($url) - strlen($last) - 1);
            }
            return [
                'photo_id' => $number,
                'url'      => $url,
            ];
        };

        $info         = explode('/', $matches[1]);
        $Data['size'] = $info[0];
        if (count($info) == 2) {
            $Data = array_merge($Data, $fn($info[1]));
            $res  = Urls::where('url', '=', $Data['url'])->first();
            if ($res && $res->entity_id) {
                $Data['entity']    = $res->entity;
                $Data['entity_id'] = $res->entity_id;
            }
        }
        else if (count($info) == 3) {
            $ent = explode('_', $info[1]);
            foreach ($ent as &$v) {
                $v = ucfirst($v);
            }
            $Data['entity'] = implode('', $ent);
            $Data           = array_merge($Data, $fn($info[2]));
            if (is_numeric($Data['url'])) {
                $Data['entity_id'] = $Data['url'];
            }
        }

        return $Data;
    }
}