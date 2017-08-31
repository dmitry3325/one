<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Vendors;

class VendorsController extends Controller
{

    public static $appSetts = [
        'title' => 'Производители',
        'js'    => ['apps/shop/vendors.js'],
        'css'   => ['apps/shop/vendors.css'],
    ];

    public function getAllFields()
    {
        return Vendors::getAllFields();
    }

    public function getVendorsList($filters = [], $fields = [])
    {
        $q = Vendors::query()->orderBy('orderBy');

        return [
            'result' => true,
            'list'   => $q->get(),
        ];
    }

    public function update($id, $data){
        $vendor = Vendors::find($id);

        foreach ($data as $key => $value) {
            $vendor->$key = $value;
        }
        $vendor->save();

        return $vendor;

    }

    public function delete($id){
        $vendor = Vendors::find($id)->delete();

        return [
            'result' => $vendor,
        ];

    }

    public function imgupload($img = null){
        var_dump($img);exit();

    }

}