<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Icon Sets
    |--------------------------------------------------------------------------
    |
    | 청담원 아이콘 세트 — Figma 디자인 시스템에서 추출한 SVG.
    | 사용: <x-icon-bell class="w-6 h-6 text-primary" />  (prefix 생략 시 set 기본 prefix 'icon')
    |   또는 @svg('icon-bell', 'w-6 h-6')
    |
    */

    'sets' => [

        'default' => [
            'paths' => [
                'resources/svg/icons',
            ],
            'prefix' => 'icon',
            'fallback' => '',
            'class' => '',
            'attributes' => [
                // 기본 크기는 컴포넌트에서 w-/h- 유틸로 지정. 비지정 시 1em.
                'width' => '1em',
                'height' => '1em',
                'aria-hidden' => 'true',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Set Attributes & Class
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        //
    ],

];
