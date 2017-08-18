module.exports = {
    'tab_1': {
        'title': 'Общая информация',
        'content': [
            [
                {
                    'field': 'id',
                    'size': 3
                },
                {
                    'field': 'parent_id',
                    'size': 3
                },
                {
                    'field': 'orderby',
                    'size': 3
                },
                {
                    'field': 'hidden',
                    'size': 3
                },
            ], [
                {
                    'field': 'title',
                    'size': 12
                },
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
                    'field': 'short_description',
                    'size': 12
                }
            ]
        ]
    },
    'tab_2': {
        'title': 'Фильтры',
        'content': require('./filters.js')
    },
    'tab_3': {
        'title': 'Фото',
        'function': 'showPhotoEditor'
    },
    'tab_4': {
        'title': 'SEO',
        'content': [
            [
                {
                    'field': 'html_title',
                    'size': 12
                },
            ],[
                {
                    'field': 'html_keywords',
                    'size': 12
                },
            ],[
                {
                    'field': 'html_description',
                    'size': 12
                },
            ],
        ]
    }
};