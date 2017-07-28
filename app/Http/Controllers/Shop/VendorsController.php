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
        $q = Vendors::query();

        return [
            'result' => true,
            'list'   => $q->get(),
        ];
    }

    public function getBaseFields()
    {
        return Vendors::getBaseFields();
    }

}