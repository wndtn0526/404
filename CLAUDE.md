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

청담원 DS 를 그대로 쓴다. 프라이머리는 **Teal `#3694ab`** (코랄 아님 — `cdw-handoff/tokens.css` 는 낡은 파일이다).

**값의 흐름**

```
Figma "청담원 디자인 시스템"  (정본)
  → 청담원 플랫폼 tailwind.config.js       (Tailwind 3 · theme.extend)
  → 이 저장소 resources/css/tokens.css     (Tailwind 4 · @theme)
```

한쪽만 고치면 같은 이름의 토큰이 다른 색을 뜻하게 된다. 셋을 함께 맞춘다.
`TokenDriftTest` 가 이 대조를 자동화한다 — 청담원 저장소가 로컬에 있으면 실제로 돈다:

```bash
CDW_PLATFORM_PATH=~/cheongdamwon-platform vendor/bin/sail artisan test --filter=TokenDrift
```

**규칙**

- **뷰에 raw hex 를 쓰지 않는다.** 색은 토큰 유틸리티로만
  (`bg-primary` · `text-label-normal` · `border-line-solid-normal` · `shadow-elevation-md`).
  `DesignSystemTest::test_no_raw_hex_colors_in_views` 가 이걸 막는다.
- Tailwind 기본 팔레트(`slate-500`, `gray-50` …)도 쓰지 않는다. 지우진 않았지만 DS 밖이다.
- 칩·버튼·배지를 손으로 만들지 않는다. `resources/views/components/` 에 이미 있다.
- 아이콘은 DS 세트 219종. `<x-icon-{이름} class="h-5 w-5" />` — 청담원과 이름이 같다.
- **새 컴포넌트를 만들면 `/styleguide` 에도 추가한다.** 그래야 깨진 걸 눈으로 본다.
- ⚠️ Tailwind 는 파일 내용을 문자열로 훑는다. `bg-{$token}` 처럼 런타임에 클래스명을
  조립하면 CSS 가 생성되지 않는다. 배열엔 완성된 클래스명(`'bg-primary'`)을 담는다.

**전자결재에서 추가한 것** (청담원 DS 에 없음)

- `<x-button variant="danger">` — 반려·삭제. 승인 버튼과 색이 겹치면 사고가 난다.
- `<x-doc-status :status="…">` — 문서 상태 배지. 값은 `App\Enums\DocumentStatus`.
- `<x-approval-line :steps="…">` — 결재선. 단계 상태는 `App\Enums\ApprovalStepStatus`.

상태의 라벨·색·아이콘·**허용 전이**는 전부 enum 에 있다. 뷰에서 상태를 판단하지 말고 enum 에 묻는다.

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

- **결재선·승인 이력은 append-only로 남긴다.** 승인/반려 기록을 UPDATE로 덮어쓰지 않는다.
  누가 언제 무엇을 승인했는지가 이 시스템의 존재 이유다.
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

개발환경 + 디자인 시스템까지. **도메인 코드(모델·마이그레이션·서비스)는 아직 없다.**

- `app/Enums/DocumentStatus.php` · `ApprovalStepStatus.php` — 상태 정의만 있고 이걸 쓰는 모델은 없다.
- `/styleguide` 의 결재선·표 데이터는 뷰에 박아둔 예시다. DB 에서 오지 않는다.
- `⚡health-check.blade.php` 와 `/health` 는 환경 점검용. 실제 기능이 붙으면 지운다.
- `/styleguide` 는 로컬 전용. 운영에 올릴 땐 환경 가드나 인증 미들웨어를 붙인다.
