#!/usr/bin/env bash
#
# /styleguide 를 정적 HTML 로 뽑아 docs/ 에 넣는다. GitHub Pages 가 docs/ 를 서빙한다.
#
# 서버 붙이기 전까지 팀에 디자인 시스템을 보여주는 용도다.
# Laravel Forge 로 실서버가 붙으면 이 스크립트는 필요 없어진다 —
# 그때는 /styleguide 에 인증 미들웨어를 붙여서 쓰면 된다.
#
# 사용: ./scripts/export-styleguide.sh [포트]
#
set -euo pipefail

PORT="${1:-8001}"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$ROOT/docs"
SRC="http://127.0.0.1:${PORT}"

cd "$ROOT"

# 컴파일된 Blade 캐시를 먼저 비운다. app.css 가 storage/framework/views 를 @source 로
# 스캔하기 때문에, 삭제된 컴포넌트의 낡은 캐시가 남아 있으면 죽은 유틸리티 클래스가
# 그대로 CSS 에 실려 나간다.
echo "▸ 뷰 캐시 정리"
php artisan view:clear

echo "▸ 에셋 빌드"
npm run build --silent

echo "▸ 임시 서버 기동 (:${PORT})"
php artisan serve --host=127.0.0.1 --port="$PORT" >/dev/null 2>&1 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null || true' EXIT

# 서버가 뜰 때까지 대기
for _ in $(seq 1 40); do
    if curl -sf -o /dev/null "${SRC}/styleguide"; then break; fi
    sleep 0.5
done

if ! curl -sf -o /dev/null "${SRC}/styleguide"; then
    echo "✗ 서버가 뜨지 않았다. .env 의 SESSION_DRIVER/CACHE_STORE 가 file 인지 확인할 것" >&2
    exit 1
fi

echo "▸ HTML 추출"
rm -rf "$OUT"
mkdir -p "$OUT"
curl -s "${SRC}/styleguide" > "$OUT/index.html"

echo "▸ 에셋 복사"
cp -R "$ROOT/public/build" "$OUT/build"

echo "▸ 경로·스크립트 치환"
python3 - "$OUT/index.html" "$SRC" <<'PY'
import re, sys

path, src = sys.argv[1], sys.argv[2]
html = open(path, encoding='utf-8').read()

# 절대 URL → 상대 경로 (Pages 는 /<repo>/ 하위에 서빙된다)
html = html.replace(f'{src}/build/', './build/')

# Livewire 런타임은 정적 페이지에 없다. Alpine 만 CDN 으로 대체한다.
# (스타일가이드는 Livewire 컴포넌트를 쓰지 않고 Alpine 만 쓴다)
html = re.sub(r'<script[^>]*livewire[^>]*></script>', '', html, flags=re.I)
html = html.replace(
    '</head>',
    '    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>\n'
    '    <meta name="robots" content="noindex">\n'
    '</head>',
)

# 남은 절대 URL 이 있으면 빌드를 실패시킨다 (조용히 깨진 링크가 나가는 것보다 낫다)
leftover = re.findall(re.escape(src) + r'[^"\']*', html)
if leftover:
    sys.exit(f'✗ 상대 경로로 못 바꾼 URL 이 남았다: {sorted(set(leftover))[:5]}')

open(path, 'w', encoding='utf-8').write(html)
print(f'  HTML {len(html):,}자')
PY

# Jekyll 이 _ 로 시작하는 파일을 무시하지 않도록
touch "$OUT/.nojekyll"

echo "✓ docs/ 생성 완료 — GitHub Pages 소스를 main 브랜치 /docs 로 설정하면 서빙된다"
