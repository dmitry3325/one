<?php

namespace App\Models\Photos;

use \App\Models\BaseModel;

/**
 * Class TempPhotos
 * @package App\Models\Photos
 */
class TempPhotos extends BaseModel
{
    protected $table   = 'photos.temp_photos';
    protected $guarded = ['id'];

    /**
     * @param $entity
     * @param $id
     * @param null $num
     *
     * @return bool
     */
    public static function deleteFiles($entity, $id, $num = null)
    {
        $q = self::where('entity', '=', $entity)->where('entity_id', '=', $id);
        if ($num) {
            $q->where('photo_id', '=', $num);
        }
        $photos = $q->get();
        foreach ($photos as $photo) {
            $path = Photos::getBasePublicPath() . $photo->path;
            if (file_exists($path)) {
                unlink($path);
            }
            $photo->delete();
        }
        return true;
    }
}