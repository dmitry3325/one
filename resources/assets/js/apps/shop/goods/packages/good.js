let good = Funs.cloneObject(require('./base.js'));

good['common']['content'] = [
    [
        {
            'field': 'id',
            'size': 2
        },
        {
            'field': 'parent_id',
            'size': 2
        },
        {
            'field': 'orderby',
            'size': 2
        },
        {
            'field': 'orderby_manual',
            'size': 2
        },
        {
            'field': 'orderby_auto',
            'size': 2
        },
        {
            'field': 'hidden',
            'size': 2
        },
    ], [
        {
            'content': '<hr>',
            'size': 12
        }
    ], [
        {
            'field': 'title',
            'size': 12
        },
    ], [
        {
            'field': 'type',
            'size': 2
        }, {
            'field': 'manid',
            'size': 4
        }, {
            'field': 'articul',
            'size': 3
        }, {
            'field': 'sarticul',
            'size': 3
        }
    ], [
        {
            'field': 'url',
            'size': 12,
            'add': {
                'el': 'button',
                'title': 'Сгенерировать URL',
                'function': 'generateUrlFromTitle'
            }
        },
    ], [
        {
            'field': 'h1_title',
            'size': 6
        },
        {
            'field': 'path_title',
            'size': 6
        },
    ], [
        {
            'content': '<hr>',
            'size': 12
        }
    ], [
        {
            'field': 'weight',
            'size': 2
        }, {
            'field': 'img_new',
            'size': 2
        }, {
            'field': 'img_promo',
            'size': 2
        }, {
            'field': 'comments_avg',
            'size': 3
        }, {
            'field': 'comments_num',
            'size': 3
        },
    ], [
        {
            'field': 'short_description',
            'size': 12
        }
    ], [
        {
            'field': 'big_description',
            'size': 12
        }
    ], [
        {
            'field': 'not_for_ya_market',
            'size': 4
        }, {
            'field': 'not_for_site_map',
            'size': 4
        }
    ]
];
good['prices'] = {
    'title': 'Цены',
    'content':
        [
            [
                {
                    'field': 'show_buy',
                    'size': 3,
                },
                {
                    'field': 'min_qty',
                    'size': 3,
                },
                {
                    'field': 'ignore_min_qty',
                    'size': 3,
                },

            ],
            [
                {
                    'field': 'stop_sale',
                    'size': 3,
                },
                {
                    'field': 'cancelled',
                    'size': 3,
                }
            ],
            [
                {
                    'content': '<hr>',
                    'size': 12
                }
            ],
            [
                {
                    'field': 'price',
                    'size': 3,
                },
                {
                    'field': 'final_price',
                    'size': 3,
                },
                {
                    'field': 'discount',
                    'size': 3,
                },
            ],
            [
                {
                    'field': 'final_price_round_method',
                    'size': 3,
                },
            ],
            [
                {
                    'content': '<hr>',
                    'size': 12
                }
            ],
            [
                {
                    'field': 'cost',
                    'size': 3,
                },
                {
                    'field': 'tarif',
                    'size': 3,
                }
                ,
                {
                    'field': 'tarif_discount',
                    'size': 3,
                }
            ],
            [
                {
                    'field': 'price_opt1',
                    'size': 3,
                },
                {
                    'field': 'price_opt1',
                    'size': 3,
                }
            ],
        ],
};
good['good_links'] = {
    'title': 'Связанные товары / Компоненты',
    'content': [
        [
            {
                'field': 'components',
                'size': 12
            }
        ],
        [
            {
                'content': '<hr>',
                'size': 12
            }
        ],
        [
            {
                'field': 'links_title',
                'size': 12
            }
        ],
        [
            {
                'field': 'links_list',
                'size': 12
            }
        ]
    ]
};

module.exports = good;