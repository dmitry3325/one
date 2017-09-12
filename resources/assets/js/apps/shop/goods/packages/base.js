module.exports = {
    'common': {
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
            ], [
                {
                    'field': 'big_description',
                    'size': 12
                }
            ]
        ]
    },
    'filters': {
        'title': 'Фильтры',
        'component': 'filters'
    },
    'photos': {
        'title': 'Фото',
        'component': 'photos'
    },
    'seo': {
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