<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Icon Sets
    |--------------------------------------------------------------------------
    |
    | 아이콘 세트 219종 — 청담원 DS Figma 에서 추출한 SVG.
    | (색·타이포 토큰은 원본 로 옮겼지만 아이콘은 아직 이 출처를 쓴다.)
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

        /*
        | 브랜드 마크 — DS 아이콘이 아니다. 세트를 나눈 이유:
        |  · 색이 SVG 안에 박혀 있다(투톤). currentColor 로 물들지 않는다.
        |  · resources/svg/icons 는 219종이라는 개수 자체가 테스트로 고정돼 있다.
        | 사용: <x-brand-cdw-mark class="h-[13.33px] w-4" />
        */
        'brand' => [
            'paths' => [
                'resources/svg/brand',
            ],
            'prefix' => 'brand',
            'fallback' => '',
            'class' => '',
            'attributes' => [
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
