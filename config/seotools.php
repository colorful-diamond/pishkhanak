<?php
/**
 * @see https://github.com/artesaos/seotools
 */

return [
    'meta' => [
        /*
         * The default configurations to be used by the meta generator.
         */
        'defaults'       => [
            'title'        => "پیشخوانک", // set false to total remove
            'titleBefore'  => false, // Put defaults.title before page title, like 'It's Over 9000! - Dashboard'
            'description'  => 'پیشخوانک، مرجع جامع خدمات استعلام آنلاین شامل استعلام بانکی، خودرو، مالیاتی، جواز کسب و سایر خدمات ضروری. آسان، سریع و معتبر.', // set false to total remove
            'separator'    => ' - ',
            'keywords'     => ['استعلام آنلاین', 'کارت به شبا', 'استعلام خودرو', 'خلافی خودرو', 'استعلام مالیاتی', 'جواز کسب', 'شناسه ملی', 'پیشخوانک'],
            'canonical'    => 'current', // Set to null or 'full' to use Url::full(), set to 'current' to use Url::current(), set false to total remove
            'robots'       => 'index, follow', // Set to 'all', 'none' or any combination of index/noindex and follow/nofollow
        ],
        /*
         * Webmaster tags are always added.
         */
        'webmaster_tags' => [
            'google'    => null,
            'bing'      => null,
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
            'norton'    => null,
        ],

        'add_notranslate_class' => false,
    ],
    'opengraph' => [
        /*
         * The default configurations to be used by the opengraph generator.
         */
        'defaults' => [
            'title'       => 'پیشخوانک - استعلام هر آنچه که می خواهید!', // set false to total remove
            'description' => 'پیشخوانک، مرجع جامع خدمات استعلام آنلاین شامل استعلام بانکی، خودرو، مالیاتی، جواز کسب و سایر خدمات ضروری. آسان، سریع و معتبر.', // set false to total remove
            'url'         => null, // Set null for using Url::current(), set false to total remove
            'type'        => 'website',
            'site_name'   => 'پیشخوانک',
            'images'      => ['assets/logo-lg.png'],
        ],
    ],
    'twitter' => [
        /*
         * The default values to be used by the twitter cards generator.
         */
        'defaults' => [
            'card'        => 'summary_large_image',
            'site'        => '@estelam_net',
        ],
    ],
    'json-ld' => [
        /*
         * The default configurations to be used by the json-ld generator.
         */
        'defaults' => [
            'title'       => 'پیشخوانک - استعلام هر آنچه که می خواهید!', // set false to total remove
            'description' => 'پیشخوانک، مرجع جامع خدمات استعلام آنلاین شامل استعلام بانکی، خودرو، مالیاتی، جواز کسب و سایر خدمات ضروری. آسان، سریع و معتبر.', // set false to total remove
            'url'         => null, // Set to null or 'full' to use Url::full(), set to 'current' to use Url::current(), set false to total remove
            'type'        => 'WebPage',
            'images'      => ['assets/logo-lg.png'],
        ],
    ],
];