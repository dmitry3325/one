<?php

namespace App\Models\Photos;

use App\Services\Common\Images;
use Illuminate\Database\Eloquent\Model;

class Photos extends Model
{
    protected $table   = 'photos.photos';
    protected $guarded = ['id'];

    const PIC_PATH    = '/p';
    const STORAGE_DIR = '/photos';

    public static $sizes = [
        'thumb'  => [
            'width'         => 60,
            'height'        => 60,
            'no_water_mark' => true,
        ],
        'small'  => [
            'width'  => 180,
            'height' => 180,
        ],
        'medium' => [
            'width'  => 400,
            'height' => 400,
        ],
        'big'    => [
            'width'  => 800,
            'height' => 800,
        ],
    ];

    public static function addImg($entity, $id, Images $img, $props = [])
    {
        $e = $entity::findOrFail($id);

        $photo_id = 1;
        if ($max = self::select(\DB::raw('count(*) as count'))->where('entity', '=', $entity)
            ->where('entity_id', '=', $id)->first()) {
            $photo_id = $max->count + 1;
        }

        $path = self::getOriginalPath($entity, $id, $photo_id, $img->getExtension());
        self::saveFile($img, $path);

        self::create([
            'entity'    => $entity,
            'entity_id' => $id,
            'photo_id'  => $photo_id,
            'width'     => $img->getWidth(),
            'height'    => $img->getHeight(),
            'filetype'  => $img->getMimeType(),
            'path'      => self::getBaseStoragePath().$path,
            'hash'      => $img->getCrc32(),
        ]);

        return $e->savePhotos();
    }

    public function deleteImg($entity, $id, $num = 'all')
    {
        $e = $entity::findOrFail($id);
        if ($num == 'all') {
            rmdir(self::getBaseStoragePath() . $entity::getTableName(true) . '/' . $id . '/');
            $e->photos()->delete();
        }
        else if (is_numeric($num)) {
            $ph = $e->photos()->where('photo_id', '=', $num)->first();
            if ($ph) {
                unlink($ph->path);
                $ph->delete();
            }
        }

        self::resort($entity, $id);

        return $e->savePhotos();
    }

    public function resort($entity, $id)
    {

    }

    public static function saveFile($img, $img_path, $basePath = null)
    {
        if (!$basePath) {
            $basePath = self::getBaseStoragePath();
        }
        $directories = explode('/', $img_path);
        $fileName    = array_pop($directories);
        foreach ($directories as $dir) {
            $path = $basePath . $dir . '/';
            if (!is_dir($path)) {
                mkdir($path, 0775, true);
                chmod($path, 0775);
            }
            $basePath = $path;
        }

        return $img->toFile($basePath . $fileName);
    }

    public static function getOriginalPath($entity, $id, $num = 1, $extension = 'jpeg')
    {
        return $entity::getTableName(true) . '/' . $id . '/' . $num . '.' . $extension;
    }

    public static function getBaseStoragePath()
    {
        return storage_path() . self::STORAGE_DIR . '/';
    }

    public static function getBasePublicPath()
    {
        return public_path() . self::PIC_PATH . '/';
    }
}
