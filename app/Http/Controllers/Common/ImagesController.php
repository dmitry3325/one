<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Photos\Photos;
use App\Models\Photos\TempPhotos;
use App\Models\Shop\ShopBaseModel;
use App\Models\Shop\Urls;
use App\Services\Common\Images;

class ImagesController extends Controller
{

    const WATERMARK = '/images/watermarks/logo.png';

    public function index()
    {
        $Info = $this->getImageInfo();
        if (!isset($Info['entity']) || !isset($Info['entity_id']) || !ShopBaseModel::checkEntity($Info['entity'])) {
            return $this->page_404();
        }

        $photo = Photos::where('entity', '=', $Info['entity'])
            ->where('entity_id', '=', $Info['entity_id'])
            ->where('photo_id', '=', $Info['photo_id'])->first();

        $filePath = Photos::getBaseStoragePath() . $photo->path;
        if (!$photo || !file_exists($filePath) || !isset(Photos::$sizes[$Info['size']])) {
            return $this->page_404();
        }

        $img = new Images();
        $img->fromFile($filePath);
        $size = Photos::$sizes[$Info['size']];
        $img->resizeNotBigger($size['width'], $size['height']);

        $path = str_replace(Photos::PIC_PATH, '', '/' . \Request::path());
        TempPhotos::create([
            'entity'    => $Info['entity'],
            'entity_id' => $Info['entity_id'],
            'photo_id'  => $Info['photo_id'],
            'filetype'  => $Info['filetype'],
            'path'      => $path,
        ]);
        if (!isset($size['no_water_mark']) || !$size['no_water_mark']) {
            if (file_exists(public_path() . self::WATERMARK)) {
                $img->overlay(new Images(public_path() . self::WATERMARK), 'bottom', 0.5, 0, -20);
            }
        }

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

    /**
     * Images function
     */
    public function getEntityPhotos($entity, $id, $ext = 'jpg')
    {
        if (!ShopBaseModel::checkEntity($entity)) {
            return [];
        }

        $e      = $entity::findOrFail($id);
        $photos = $e->photos()->orderBy('photo_id', 'asc')->get()->toArray();
        foreach (Photos::$sizes as $size => $photo) {
            foreach ($photos as &$p) {
                $p['urls'][$size] = $e->getPhotoUrl($size, $p['photo_id'], str_replace('image/', '', $p['filetype']));
            }
        }
        return $photos;
    }

    public function uploadImgs()
    {
        $id     = \Request::get('id');
        $entity = \Request::get('entity');
        $images = \Request::file('images');

        if (!ShopBaseModel::checkEntity($entity)) {
            return [];
        }

        if ($images) {
            foreach ($images as $k => $img) {
                Photos::addImg($entity, $id, new Images($img->path()));
            }
        }
        else if (\Request::get('image')) {
            Photos::addImg($entity, $id, new Images(\Request::get('image')));
        }

        return [
            'result' => true,
        ];
    }

    public function reOrder($entity, $id, $new_order)
    {
        $res = ['result' => false];
        if (!ShopBaseModel::checkEntity($entity)) {
            return $res;
        }

        return Photos::reOrderImgs($entity, $id, $new_order);
    }

    public function rotateImg($entity, $id, $num, $side)
    {
        $res = ['result' => false];
        if (!ShopBaseModel::checkEntity($entity)) {
            return $res;
        }

        return Photos::rotateImg($entity, $id, $num, $side);
    }

    public function deleteImg($entity, $id, $num)
    {
        $res = ['result' => false];
        if (!ShopBaseModel::checkEntity($entity)) {
            return $res;
        }

        return Photos::deleteImg($entity, $id, $num);
    }

    public function toggleHideImg($entity, $id, $num, $hide)
    {
        $res = ['result' => false];
        if (!ShopBaseModel::checkEntity($entity)) {
            return $res;
        }

        $e          = $entity::findOrFail($id);
        $ph         = Photos::where('entity', '=', $entity)->where('entity_id', '=', $id)->where('photo_id', '=',
            $num)->first();
        $ph->hidden = intval($hide);
        $ph->save();
        $e->savePhotos();
        $res['result'] = true;
        return $res;
    }

    public function reloadImages($entity, $id)
    {
        $res = ['result' => false];
        if (!ShopBaseModel::checkEntity($entity)) {
            return $res;
        }
        $res['result'] = TempPhotos::deleteFiles($entity, $id);
        return $res;
    }
}