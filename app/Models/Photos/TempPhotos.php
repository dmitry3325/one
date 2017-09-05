<?php

namespace App\Models\Photos;

use Illuminate\Database\Eloquent\Model;

class TempPhotos extends Model
{
    protected $table   = 'photos.temp_photos';
    protected $guarded = ['id'];

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