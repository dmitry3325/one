<?php

namespace App\Http\Controllers\Goods;
use App\Classes\Packages\LinePackage;
use App\Http\Controllers\Controller;

class GoodsController extends Controller{

    public static $appSetts = [
        'title' => 'Товары',
    ];

    public function index(){
        return view('apps.goods.goods');
    }

    public function getGoods($id){
        return ['aaaa'=>$id];
    }
}