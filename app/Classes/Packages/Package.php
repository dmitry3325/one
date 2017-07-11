<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 05.07.17
 * Time: 18:56
 */

namespace App\Classes\Packages;

abstract class Package
{
    protected $package = [];

    private function preparePackage()
    {

    }

    public function getPackage()
    {
        $this->preparePackage();
        return $this->package;
    }
}