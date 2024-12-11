<?php

return [
    'feeds' => [
        'main' => [
            /*
             * Here you can specify which class and method will return
             * the items that should appear in the feed. For example:
             * 'App\Model@getAllFeedItems'
             *
             * You can also pass an argument to that method:
             * ['App\Model@getAllFeedItems', 'argument']
             */
            'items' => '\Backpack\NewsCRUD\app\Models\Article@getFeedItems',

            /*
             * The feed will be available on this url.
             */
            'url' => '/feed',

            'title' => 'Zagorodna RSS Feed',
            'description' => 'Zagorodna RSS Feed.',
            'language' => 'ru-RU',

            /*
             * The view that will render the feed.
             */
            'view' => 'feed.rss',

            /*
             * The type to be used in the <link> tag
             */
            'type' => 'application/atom+xml',
        ],
        'uk' => [
            /*
             * Here you can specify which class and method will return
             * the items that should appear in the feed. For example:
             * 'App\Model@getAllFeedItems'
             *
             * You can also pass an argument to that method:
             * ['App\Model@getAllFeedItems', 'argument']
             */
            'items' => '\Backpack\NewsCRUD\app\Models\Article@getFeedItems',

            /*
             * The feed will be available on this url.
             */
            'url' => '/feed?lang=uk',

            'title' => 'Zagorodna RSS Feed',
            'description' => 'Zagorodna RSS Feed.',
            'language' => 'uk-UA',

            /*
             * The view that will render the feed.
             */
            'view' => 'feed.rss',

            /*
             * The type to be used in the <link> tag
             */
            'type' => 'application/atom+xml',
        ],
    ],
];
