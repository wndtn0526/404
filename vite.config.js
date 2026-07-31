import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// 폰트는 Pretendard 를 app.css 에서 CDN 로드한다(청담원 플랫폼과 동일).
// bunny() 는 Google Fonts 미러라 Pretendard 가 없어서 쓰지 않는다.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
