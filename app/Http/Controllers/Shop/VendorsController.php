<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Vendors;

/**
 * Class VendorsController
 * @package App\Http\Controllers\Shop
 */
class VendorsController extends Controller
{

    public static $appSetts = [
        'title' => 'Производители',
        'js'    => ['apps/shop/vendors.js'],
        'css'   => ['apps/shop/vendors.css'],
    ];

    /**
     * @return array
     */
    public function getAllFields()
    {
        return Vendors::getAllFields();
    }

    /**
     * @param array $filters
     * @param array $fields
     *
     * @return array
     */
    public function getVendorsList($filters = [], $fields = [])
    {

        $q = Vendors::query()->orderBy('id', 'DESC');

        return [
            'result' => true,
            'list'   => $q->get(),
        ];
    }

    /**
     * @param $id
     * @param $data
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function update($id, $data){
        $vendor = Vendors::find($id);

        foreach ($data as $key => $value) {
            $vendor->$key = $value;
        }
        $vendor->save();

        return $vendor;

    }

    /**
     * @param $id
     *
     * @return array
     */
    public function delete($id){
        $vendor = Vendors::find($id)->delete();

        return [
            'result' => $vendor,
        ];

    }

    /**
     * @return array
     */
    public function create(){
        $vendor =  new Vendors();
        $result = $vendor->save();

        $vendor = Vendors::find($vendor->id);

        return [
            'result' => $result,
            'vendor' => $vendor,
        ];

    }

    /**
     * @param null $img
     */
    public function imgupload($img = null){
        var_dump($img);exit();

    }

}