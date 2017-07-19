<?php

namespace App\Http\Controllers\Goods;
use App\Classes\Packages\LinePackage;
use App\Http\Controllers\Controller;

class GoodsController extends Controller{

    public static $appSetts = [
        'title' => 'Товары',
        'js' => ['apps/goods/goods.js'],
        'css' => ['apps/goods/goods.css']
    ];


    public function getGoods($id){
        return ['aaaa'=>$id];
    }
}