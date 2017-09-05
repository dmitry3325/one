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
            'width'         => 80,
            'height'        => 60,
            'no_water_mark' => true,
        ],
        'small'  => [
            'width'  => 160,
            'height' => 120,
            'no_water_mark' => true,
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

    public function getExtension()
    {
        return str_replace('image/', '', $this->filetype);
    }

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
            'path'      => $path,
            'hash'      => $img->getCrc32(),
        ]);

        return $e->savePhotos();
    }

    public static function deleteImg($entity, $id, $num = 'all')
    {
        $e = $entity::findOrFail($id);
        if ($num == 'all') {
            rmdir(self::getBaseStoragePath() . $entity::getTableName(true) . '/' . $id . '/');
            $e->photos()->delete();
        }
        else if (is_numeric($num)) {
            $ph = $e->photos()->where('photo_id', '=', $num)->first();
            if ($ph) {
                unlink(self::getBaseStoragePath().$ph->path);
                $ph->delete();
            }
        }

        self::reOrderImgs($entity, $id);

        return $e->savePhotos();
    }

    public static function reOrderImgs($entity, $id, $new_order = [])
    {
        $e = $entity::findOrFail($id);

        $afterNum = 1000;
        $toRename = [];
        $photos   = self::where('entity', '=', $entity)->where('entity_id', '=', $id)->orderBy('photo_id','asc')->get()->keyBy('photo_id');
        if(!count($new_order)){
            foreach($photos as $p){
                $new_order[] = $p->photo_id;
            }
        }
        foreach ($new_order as $k => $oldNum) {
            $newNum = $k + 1;
            if (isset($photos[$oldNum]) && $oldNum != $newNum) {
                $photo           = $photos[$oldNum];
                $photo->photo_id = $afterNum + $newNum;

                $toRename[] = $photo->photo_id;
                $oldPath    = self::getBaseStoragePath() . $photo->path;
                $newPath    = self::getBaseStoragePath() . self::getOriginalPath($entity, $id, $photo->photo_id,
                        $photo->getExtension());
                rename($oldPath, $newPath);
                $photo->path = self::getOriginalPath($entity, $id, $newNum, $photo->getExtension());
                $photo->save();
            }
        }

        foreach ($toRename as $num) {
            $newNum = $num - $afterNum;
            $old    = self::getBaseStoragePath() . self::getOriginalPath($entity, $id, $num, $photo->getExtension());
            $new    = self::getBaseStoragePath() . self::getOriginalPath($entity, $id, $newNum, $photo->getExtension());
            rename($old, $new);
            Photos::where('entity', '=', $entity)->where('entity_id', '=', $id)->where('photo_id', '=', $num)
                ->update([
                    'photo_id' => $newNum,
                ]);
        }

        $e->savePhotos();
        TempPhotos::deleteFiles($entity, $id);
        return $photos;
    }

    public static function rotateImg($entity, $id, $num, $side){
        $e = $entity::findOrFail($id);

        $ph = $e->photos()->where('photo_id', '=', $num)->first();

        $path = self::getBaseStoragePath().$ph->path;
        $im = new Images($path);
        $im->rotate($side);
        $im->toFile($path);
        $ph->width = $im->getWidth();
        $ph->height = $im->getHeight();
        TempPhotos::deleteFiles($entity, $id, $num);
        return ['result'=>$ph->save()];
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
