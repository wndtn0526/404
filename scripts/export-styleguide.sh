#!/usr/bin/env bash
#
# 화면을 정적 HTML 로 뽑아 docs/ 에 넣는다. GitHub Pages 가 docs/ 를 서빙한다.
#
# 서버 붙이기 전까지 팀에 화면을 보여주는 용도다.
# Laravel Forge 로 실서버가 붙으면 이 스크립트는 필요 없어진다 —
# 그때는 각 라우트에 인증 미들웨어를 붙여서 쓰면 된다.
#
# 사용: ./scripts/export-styleguide.sh [포트]
#
set -euo pipefail

PORT="${1:-8001}"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$ROOT/docs"
SRC="http://127.0.0.1:${PORT}"

# 뽑을 페이지 — "라우트:출력파일". 출력은 docs/ 바로 아래 평평하게 둔다.
# 하위 디렉터리로 내리면 ./build/ 상대경로가 깨진다.
PAGES=(
    "styleguide:index.html"
    "contents:contents.html"
    "contents/new:contents-new.html"
    "contents/detail:contents-detail.html"
    "courses:courses.html"
    "courses/new:courses-new.html"
    "courses/detail:courses-detail.html"
    "organization:organization.html"
    "orgs:orgs.html"
    "orgs/history:orgs-history.html"
    "finance:finance.html"
    "finance/budget:finance-budget.html"
    "finance/personal:finance-personal.html"
    "finance/expense:finance-expense.html"
    "documents:documents.html"
    "documents/new:documents-new.html"
    "documents/new-done:documents-new-done.html"
    "documents/new-crowd:documents-new-crowd.html"
    "documents/vacation:documents-vacation.html"
    "documents/vacation-done:documents-vacation-done.html"
    "documents/vendor:documents-vendor.html"
    "documents/vendor-done:documents-vendor-done.html"
    "documents/review:documents-review.html"
    "documents/review-empty:documents-review-empty.html"
    "public-space:public-space.html"
    "public-space-empty:public-space-empty.html"
    "profile-settings:profile-settings.html"
    "post:post.html"
    "workspace:workspace.html"
)

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

rm -rf "$OUT"
mkdir -p "$OUT"

echo "▸ 에셋 복사"
cp -R "$ROOT/public/build" "$OUT/build"

# 라우트 → 파일 매핑을 파이썬에 그대로 넘긴다(내부 링크 치환에 필요).
MAPPING="$(printf '%s\n' "${PAGES[@]}")"

for page in "${PAGES[@]}"; do
    route="${page%%:*}"
    file="${page##*:}"

    echo "▸ /${route} → docs/${file}"

    if ! curl -sf -o "$OUT/$file" "${SRC}/${route}"; then
        echo "✗ /${route} 를 가져오지 못했다" >&2
        exit 1
    fi

    python3 - "$OUT/$file" "$SRC" "$MAPPING" <<'PY'
import re, sys

path, src, mapping = sys.argv[1], sys.argv[2], sys.argv[3]
html = open(path, encoding='utf-8').read()

# 에셋 절대 URL → 상대 경로 (Pages 는 /<repo>/ 하위에 서빙된다)
html = html.replace(f'{src}/build/', './build/')

# 페이지끼리의 내부 링크도 정적 파일명으로 바꾼다. 안 바꾸면 아래 검사에서 걸린다.
# 긴 라우트부터 치환해야 접두어가 겹칠 때 짧은 쪽이 먼저 먹지 않는다.
pairs = [line.split(':', 1) for line in mapping.splitlines() if line.strip()]
for route, out_file in sorted(pairs, key=lambda p: -len(p[0])):
    html = html.replace(f'{src}/{route}', f'./{out_file}')

# 루트 링크(로고 등)는 스타일가이드로 보낸다. Pages 루트가 곧 index.html 이다.
html = html.replace(f'"{src}/"', '"./index.html"').replace(f'"{src}"', '"./index.html"')

# Livewire 런타임은 정적 페이지에 없다. Alpine 만 CDN 으로 대체한다.
# (뽑는 화면들은 Livewire 컴포넌트를 쓰지 않고 Alpine 만 쓴다)
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
print(f'  {len(html):,}자')
PY
done

# Jekyll 이 _ 로 시작하는 파일을 무시하지 않도록
touch "$OUT/.nojekyll"

echo "✓ docs/ 생성 완료 — GitHub Pages 소스를 main 브랜치 /docs 로 설정하면 서빙된다"
