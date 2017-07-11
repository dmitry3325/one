<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 05.07.17
 * Time: 18:56
 */

namespace App\Classes\Packages;

class GoodsPackage extends Package
{
    public $type = "";

    protected $package = [
        'tab_1' => [
            [
                "title"  => "Общая информация",
                "fields" => [
                    "parent_id"            => [
                        "title"     => "Папка",
                        "size"      => "2",
                        "type"      => "int",
                        "end_block" => true,
                    ],
                    "id"                   => [
                        "title"    => "ID",
                        "size"     => "2",
                        "type"     => "int",
                        "readonly" => 1,
                    ],
                    "hidden"               => [
                        "title"     => "Отображен",
                        "size"      => "2",
                        "type"      => "checkbox",
                        "end_block" => true,
                    ],
                    "order_by"             => [
                        "title" => "Ручной приоритет",
                        "size"  => "2",
                        "type"  => "int",
                    ],
                    "order_by_auto"        => [
                        "title"    => "Автоприоритет",
                        "size"     => "2",
                        "type"     => "int",
                        "readonly" => 1,
                    ],
                    "ignore_order_by_auto" => [
                        "title" => "Игнорировать автоприоритет",
                        "size"  => "2",
                        "type"  => "checkbox",
                    ],
                    "h1_title"             => [
                        "title" => "Заголовок H1",
                        "size"  => "12",
                    ],
                    "breadcrumbs_title"    => [
                        "title" => "Заголовок в пути",
                        "size"  => "12",
                    ],
                ],
            ],
        ],
        'tab_2' => [
            "title"  => "Оптимизация",
            "fields" => [
                "html_title" => [
                    'title' => "TITLE",
                    "size"  => "12",
                ],
                "html_description" => [
                    'title' => "DESCRIPTION",
                    "size"  => "12",
                    "type"  => "text",
                ],
                "html_keywords" => [
                    'title' => "KEYWORDS",
                    "size"  => "12",
                    "type"  => "text",
                ],
            ],
        ],
    ];
}