<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04.07.17
 * Time: 14:38
 */

namespace App\Classes\Blocks;

/**
 * Class AdminBar
 * @package App\Classes\Blocks
 */
class AdminBar
{
    private static $baseNamespace = 'App\Http\Controllers\\';
    private static $app           = [
        'shop' => [
            'title' => 'Сайт',
            'children' => [
                '' => [],
                'vendors' => []
            ]
        ],
    ];

    /**
     * @return array
     */
    public static function getMenu()
    {
        self::prepareMenu(self::$app);
        return self::$app;
    }

    /**
     * @return mixed
     */
    public static function shareMenu()
    {
        return \View::share('admin_bar', self::getMenu());
    }

    /**
     * @param $list
     * @param string $baseUrl
     */
    private static function prepareMenu(&$list, $baseUrl = '')
    {
        foreach ($list as $url => &$block) {
            $url  = (($url) ? '/' . $url : '');
            $path = $baseUrl . $url;

            if (!isset($block['url'])) {
                $block['url'] = $path;
            }

            $parts   = explode('/', $path);
            $appName = [];
            foreach ($parts as $p) {
                if (!$p) {
                    continue;
                }
                $appName[] = ucfirst($p);
            }

            $namespace = self::$baseNamespace . $appName[0] . '\\';
            if (count($appName) > 1) {
                unset($appName[0]);
            }

            $appName   = implode('', $appName);
            $className = $appName . 'Controller';
            if (isset($block['class_name'])) {
                $className = $block['class_name'];
            }

            if (isset($block['name_space'])) {
                $namespace = self::$baseNamespace . $block['name_space'];
            }
            if (class_exists($namespace . $className)) {
                $app = $namespace . $className;
                if (isset($app::$appSetts)) {
                    self::setSettings($block, $app::$appSetts);
                }
            }

            if (isset($block['children'])) {
                self::prepareMenu($block['children'], $path);
            }
        }
    }

    /**
     * @param $block
     * @param array $setts
     */
    private static function setSettings(&$block, $setts = [])
    {
        foreach ($setts as $k => $v) {
            if (!isset($block[$k])) {
                $block[$k] = $setts[$k];
            }
        }
    }
}