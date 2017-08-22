<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class GoodsPhoto extends Model
{
    protected $table = 'shop.goods_photos';

    public function customFieldsValues()
    {
        return $this->morphMany(Vendors::class, 'entity');
    }

}
