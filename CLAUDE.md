# CLAUDE.md

전자결재 시스템. 청담원 플랫폼과 동일한 TALL 스택으로 맞춘 별도 저장소.

## 스택

| 레이어 | 선택 |
| --- | --- |
| 백엔드 | Laravel 13 / PHP 8.5 |
| 프론트 | Livewire 4 + Alpine.js + Tailwind CSS 4 (Vite) |
| DB | PostgreSQL 18 |
| 캐시·세션·큐 | Redis (predis 클라이언트) |
| 로컬 환경 | Docker / Laravel Sail |
| 포매터 | Laravel Pint |

## 실행

```bash
cp .env.example .env && php artisan key:generate
vendor/bin/sail up -d
vendor/bin/sail artisan migrate
vendor/bin/sail npm install && vendor/bin/sail npm run dev
```

- `http://localhost:8000/styleguide` — 디자인 시스템 (기본 진입)
- `http://localhost:8000/health` — DB·Redis·Livewire 왕복이 초록불이면 환경 정상

포트는 `.env`에서 옮긴다 (`APP_PORT` 8000, `FORWARD_DB_PORT` 54320, `FORWARD_REDIS_PORT` 63790).
로컬에 이미 뜬 Postgres/Redis와 충돌하지 않게 기본값에서 비켜 둔 값이다.

## 디자인 시스템

출처는 Figma **디자인 가이드**. 프라이머리는 **Black `#111111`** (Warm gray/900).

**값의 흐름**

```
Figma 디자인 가이드 / Design guide (node 1002-517500)   (정본)
  → resources/design/design-tokens.json    (Dev Mode MCP `get_variable_defs` 추출본)
  → resources/css/tokens.css             (Tailwind 4 · @theme)
```

Figma 가 바뀌면 추출본을 다시 뽑아 넣고, 빨개지는 토큰을 손으로 맞춘다.
`TokenSourceTest` 가 이 대조를 자동화한다 — 외부 저장소 없이 항상 돈다:

```bash
vendor/bin/sail artisan test --filter=TokenSource
```

**2계층 구조** — 원본은 원시 팔레트 시스템이라 `label`·`line`·`fill` 같은 의미 토큰이 원본에 없다.

- **원시 계층** — Figma 변수를 그대로 옮긴 값 (`--color-deep-blue-900`, `--color-cool-gray-600` …).
  값을 바꾸려면 Figma 를 먼저 고친다.
- **의미 계층** — 컴포넌트가 실제로 쓰는 이름 (`--color-label-normal`, `--color-line-solid-normal` …).
  원시값을 역할에 배정한 것이다.

⚠️ **중립색은 Warm gray 다.** 원본은 Warm gray 와 Cool gray 를 둘 다 갖고 있지만,
컴포넌트를 실측해 보면 전부 Warm gray 계열이다 (Input 보더 `Warm gray/200` · placeholder
`Warm gray/400` · Disabled `Warm gray/100` · LNB 텍스트 `Warm gray/700`).
Cool gray 는 원본 변수라 원시 계층에 남겨 두지만 **의미 계층에서는 쓰지 않는다.**

⚠️ **인풋 Active 는 브랜드색(검정)이 아니라 `deep-blue-900` 이다.** 보더 색만 바뀌고
링·그림자는 없다. 캐럿도 같은 파랑(`caret-deep-blue-900`). 이건 원본이 그래서 그대로 따랐다.
브랜드가 검정이라고 여기까지 검정으로 맞추면 원본과 달라진다.

`[파생]` 주석이 붙은 값은 원본에 대응 단계가 없어 계산해 채운 것이다 (hover/active 명도, 반투명
보더, 텍스트용 진한 액센트, `-reading` 줄간). **Figma 에서 온 값이 아니므로 그대로 신뢰하지 않는다.**
디자이너가 해당 단계를 Figma 에 추가하면 그 값으로 교체한다. `TokenSourceTest` 가 표시 누락을 막는다.

⚠️ **아이콘 219종은 아직 청담원 DS 출처다.** 이번 교체는 색·타이포·그림자 토큰만 원본 로 옮겼다.
원본에 아이콘 세트가 있으면 별도로 이관해야 한다.

**규칙**

- **뷰에 raw hex 를 쓰지 않는다.** 색은 토큰 유틸리티로만
  (`bg-primary` · `text-label-normal` · `border-line-solid-normal` · `shadow-elevation-md`).
  `DesignSystemTest::test_no_raw_hex_colors_in_views` 가 이걸 막는다.
- Tailwind 기본 팔레트(`slate-500`, `gray-50` …)도 쓰지 않는다. 지우진 않았지만 DS 밖이다.
- 칩·버튼·배지를 손으로 만들지 않는다. `resources/views/components/` 에 이미 있다.
- 아이콘은 DS 세트 219종. `<x-icon-{이름} class="h-5 w-5" />` — 청담원과 이름이 같다.
  DS 에 없는 글리프는 `resources/svg/ext` 에 넣고 `<x-ext-{이름} />` 으로 쓴다.
  세트를 나눈 이유는 `config/blade-icons.php` 에 있다 — `svg/icons` 는 219종이라는
  개수가 테스트로 고정돼 있다. **먼저 DS 219종을 찾아보고, 없을 때만 만든다.**
- **새 컴포넌트를 만들면 `/styleguide` 에도 추가한다.** 그래야 깨진 걸 눈으로 본다.
- ⚠️ Tailwind 는 파일 내용을 문자열로 훑는다. `bg-{$token}` 처럼 런타임에 클래스명을
  조립하면 CSS 가 생성되지 않는다. 배열엔 완성된 클래스명(`'bg-primary'`)을 담는다.
- ⚠️ **`display` 유틸이 이미 붙은 요소에 `hidden` 을 토글하면 안 먹는다.** `hidden` 도
  `display` 유틸이라 `inline-flex`·`flex` 와 같은 자리를 다투고, CSS 순서로 정적 클래스가
  이긴다 — DOM 에는 `hidden` 이 있는데 화면에는 그대로 보인다. 이럴 땐 `display` 자체를
  바꾼다: `x-bind:class="열림 ? 'inline-flex' : 'hidden'"` (정적 class 에서는 뺀다).
  `<tr>`·`<li>` 처럼 `display` 유틸이 없는 곳에서는 `hidden` 토글이 그냥 먹는다.
- ⚠️ **템플릿 어디에든 `<x-…>` 를 글자로 적지 않는다.** Blade 컴포넌트 컴파일러는 주석이든
  속성값 안 문자열이든 가리지 않고 훑는다. Alpine `x-data` 안 JS 주석에 `<x-table>` 이라고
  적었더니 짝 없는 여는 태그가 돼서 그 뒤가 통째로 먹혔다 — 화면이 500 이 되고, 에러는
  `syntax error … expecting "endif"` 로 엉뚱하게 뜬다. 태그 이름만 적는다(`x-table`).
- ⚠️ **`@js()` 는 컴포넌트 속성 안에서 컴파일되지 않는다.** 일반 엘리먼트(`<li :class="…@js($x)…">`)
  에서는 되지만 `<x-table.row x-bind:class="…@js($x)…">` 처럼 컴포넌트에 쓰면 문자열이 그대로
  나간다. Alpine 식을 통째로 PHP 에서 만들어 `:속성="$식"` 으로 넘긴다.

**컴포넌트 출처** — 어디서 온 것인지 모르면 Figma 를 고쳐도 반영이 안 된다.

*원본이 있는 것* — 값을 바꿀 땐 Figma 를 먼저 고친다.

| 컴포넌트 | 원본 노드 | 실측 |
| --- | --- | --- |
| `button` | Button 1002:521078 | 반경 4 · 좌우패딩 16/14/8 |
| `input` `textarea` | Input 1002:518593 | 높이 40/32 · 반경 4 · 본문 14 · **그림자 없음** |
| `dropdown` | Dropdown 1002:524627 | 높이 40 · 반경 4 |
| `tabs` | Tab 1002:522274 | 반경 3~6 · 글자 14/15 |
| `badge` | Tag 1002:524866 | 반경 2 · 글자 10 |
| `chip` | Tag / Input Tag 1002:524895 | 반경 4 · 글자 13/14 |
| `pagination` | Pagination 1002:525313 | 반경 4 · 글자 13/15 |
| `modal` | **Dialog** 1002:519932 | 반경 4 · 그림자 있음(유일) |
| `toast` | **SnackBars** 1002:519932 | 반경 6 |
| `table/*` | Data Tables 1002:523369 | 행 56/28 · 반경 2 · 셀 패딩 16 · 본문 15 |
| `checkbox` | Checkbox 1002:524217 | **직각** |
| `card` | Card 1002:521712 | **직각** |
| `gnb` | GNB 1002:520005 | 높이 56 |
| `lnb` | LNB 1002:525753 | 너비 240 · 항목 32 · 반경 3 |
| `breadcrumb` | Tab / Breadcrumb 1002:522322 | 글자 14 |
| `tooltip` | Tooltip 1002:522381 | 반경 6 · 글자 11 |
| `thumbnail` `avatar-group` | Profile 1002:522932 | 원형 / 사각 4 |
| `field-group` | Box Only (compact) 1002:113843 | 높이 32 · 반경 4 · 구분선 1x32 |

⚠️ **원본 컴포넌트는 Dialog 를 빼면 그림자가 없다.** 폼 컨트롤에 `shadow-elevation-*` 을 붙이지 않는다.
띄운 면(드롭다운 패널·캘린더 팝오버)에만 `shadow-elevation-lg` 를 쓴다.

⚠️ **테두리 하나 안에 컨트롤 여럿을 넣을 땐 `field-group` 을 쓴다.** 칸(테두리·반경·구분선·
포커스)은 `field-group` 이 그리고, 안쪽 컨트롤은 DS 것을 `variant="bare"` 로 넣는다 —
`bare` 는 테두리·반경만 뺀 것이라 글자 크기·높이·캐럿은 DS 그대로다. `input` `dropdown` 에 있다.
포커스 테두리는 `focus-within` 이라 안쪽 어디를 눌러도 칸 하나가 통째로 살아난다.
**컨트롤을 손으로 만들어 끼우지 않는다.** 같은 값을 칸 두 개로 나눠 두고 싶으면
`field-group` 없이 각각 기본 variant 로 두면 된다 — 두 모양 다 `/styleguide` '복합 칸' 에 있다.

*원본 밖 확장* — 원본에 대응이 없다. 전자결재에 필요해서 DS 토큰만으로 만든 것들이라
Figma 를 봐도 나오지 않는다. 새로 만들 때 여기 있는지 먼저 확인한다.

- `datepicker` — 결재 문서의 기간·기한 입력. 원본에 캘린더가 없다.
- `file-dropzone` `file-item` — 첨부파일. 원본에 업로드 UI 가 없다.
- `stat-tile` — 결재함 요약 숫자.
- `switch` — 온오프 토글.
- `segmented` — 뷰 전환. **`tabs` 와 역할이 겹친다.** 새 화면에선 `tabs` 를 먼저 검토한다.
- `chip-removable` — 삭제 가능한 칩. **`chip` 의 변형이다.** 통합 대상.
- `confirm` `prompt` — Dialog 의 용도별 사전 조립. 원본은 `modal`(Dialog).
- `detail-field` — 상세 화면의 라벨 + 값 한 칸. `<dl>` 안에서 쓴다. 값이 비면 하이픈.
  DS 가이드에는 없고 GPRO_PORTFOLIO node 1002-275959 실측이다.
- `empty-state` — 목록이 비었을 때의 문구 + 액션. DS 가이드에는 없고 GPRO_PORTFOLIO
  node 1104-59420 실측이다. 위쪽 여백은 놓이는 자리마다 달라 컴포넌트가 갖지 않는다.
- `ext-heart-filled` (아이콘) — 채운 하트. DS `heart` 는 외곽선뿐이라 '이미 좋아요를
  누른' 상태를 표현할 수 없다. DS `heart.svg` 의 바깥 윤곽만 남겨 실루엣을 그대로 썼다.
- `filter-bar` — 검색 + 조건 칩 한 줄. **화면마다 필터 생김새가 갈리지 않게 이걸로 통일한다.**
  원본은 화면마다 필터 카드가 제각각이라 대응 노드를 하나로 정할 수 없었다.
- `notice-card` — "누가 무엇을 했다" + 상태·시각 + 오른쪽 액션. 확인할 문서 목록이 이걸로
  쌓인다. GPRO_PORTFOLIO node 1002-106150 실측(1200x102 · 반경 6 · 패딩 30).
  원본 이름이 '홈카드' 라 워크스페이스 홈에서도 같은 카드를 쓴다.
- `workspace-shell` — GNB + LNB + 본문을 한 판으로 묶은 껍데기. 워크스페이스 화면은 전부
  이걸 쓴다. 메뉴는 `config/workspace.php` 한 곳에서 온다 — 화면마다 배열을 따로 적지 않는다.
  폴더 펼침은 `sessionStorage` 에 남아 화면을 옮겨도 유지된다.
- `chart/xy` `chart/line` `chart/bars` `chart/donut` `chart/legend` `chart/tooltip` — 재무
  대시보드용. DS 가이드에 차트가 없다. 호버 상태는 `chart/xy` 가 들고 있고 나머지가 그걸 쓴다.
  ⚠️ 값은 SVG 를 직접 그린다. 차트 라이브러리를 새로 들이지 않는다.
- `table/empty` — 표가 비었을 때의 한 줄. `colspan` 을 준다.

**전자결재 고유** (원본에 없음)

- `<x-button variant="danger">` — 반려·삭제. 승인 버튼과 색이 겹치면 사고가 난다.

## 코드 규칙

- **로직은 Service Layer(`app/Services`)에 둔다.** 컨트롤러와 Livewire 컴포넌트는 입력 검증 →
  서비스 호출 → 뷰 반환까지만 한다. 컴포넌트에 업무 규칙을 쌓지 않는다.
- Livewire 4 단일 파일 컴포넌트는 `resources/views/components/⚡이름.blade.php`.
  라우팅은 `Route::livewire('/uri', '이름')`.
- 마이그레이션은 롤백(`down()`)까지 반드시 작성한다.
- 상태를 바꾸는 요청은 POST + CSRF. GET으로 승인·반려·삭제를 처리하지 않는다.
- 비밀값은 전부 `.env`. 코드·로그·커밋에 평문으로 남기지 않는다.
- 커밋 전 `vendor/bin/sail pint` 로 포맷을 맞춘다.

## 전자결재 도메인에서 특히 주의할 것

- 승인 권한 검사는 Policy에 모으고, 화면에서 버튼을 숨기는 것으로 대신하지 않는다.
- 문서 상태 전이(기안 → 상신 → 승인/반려 → 완료)는 한 곳에서 정의하고,
  임의의 상태 점프를 막는다.
- 첨부파일은 공개 디스크에 두지 않는다. 권한 확인 후 스트리밍한다.
- 개인정보(주민번호 등)는 이 시스템에 저장하지 않는 것을 기본으로 한다.
  꼭 필요해지면 평문 저장·평문 로그 금지, 담당자 확인부터.

## 테스트

```bash
vendor/bin/sail artisan test
```

PHPUnit. 테스트 DB는 Sail이 자동 생성하는 `eapproval_testing`.

## 현재 상태

개발환경 + 디자인 시스템까지. **도메인 코드(모델·마이그레이션·서비스·상태 enum)는 아직 없다.**
결재선·문서 상태는 새로 설계할 예정이라 기존 정의를 전부 걷어냈다.

- `/styleguide` 의 표 데이터는 뷰에 박아둔 예시다. DB 에서 오지 않는다.
- `⚡health-check.blade.php` 와 `/health` 는 환경 점검용. 실제 기능이 붙으면 지운다.
- `/styleguide` 는 로컬 전용. 운영에 올릴 땐 환경 가드나 인증 미들웨어를 붙인다.
