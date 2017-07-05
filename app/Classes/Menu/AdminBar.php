<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04.07.17
 * Time: 14:38
 */

namespace App\Classes\Menu;

class AdminBar
{
    private static $baseNamespace = 'App\Http\Controllers\\';
    private static $app = [
        'goods' => [],
    ];

    public static function getMenu(){
        self::prepareMenu(self::$app);
        return self::$app;
    }

    public static function shareMenu(){
        return \View::share('admin_bar', self::getMenu());
    }

    private static function prepareMenu(&$list, $baseUrl = ''){
        foreach($list as $url=>&$block){
            $url = (($url)?'/'.$url:'');
            $path = $baseUrl.$url;

            if(!isset($block['url'])){
                $block['url'] = $path;
            }

            $parts = explode('/', $path);
            $appName = [];
            foreach ($parts as $p){
                $appName[] = ucfirst($p);
            }
            $appName = implode('',$appName);
            $className = $appName.'Controller';
            if(isset($block['class_name'])){
                $className = $block['class_name'];
            }
            $namespace = self::$baseNamespace;
            if(isset($block['name_space'])){
                $namespace = $block['name_space'];
            }
            if(class_exists($namespace.$className)) {
                $app = $namespace.$className;
                if(isset($app::$appSetts)){
                    self::setSettings($block,$app::$appSetts);
                }
            }

            if(isset($block['children'])){
                self::prepareMenu($block['children'],$path);
            }
        }
    }

    private static function setSettings(&$block, $setts = []){
        foreach($setts as $k=>$v){
            if(!isset($block[$k])){
                $block[$k] = $setts[$k];
            }
        }
    }
}