# 404 — 전자결재

전자결재 시스템. TALL 스택으로 맞춘 별도 저장소.

Laravel 13 · Livewire 4 · Alpine.js · Tailwind CSS 4 · PostgreSQL 18 · Redis · Docker(Sail)

## 시작하기

필요한 것: Docker Desktop, PHP 8.3+, Composer. (Node는 컨테이너 안에서 쓴다.)

```bash
git clone https://github.com/wndtn0526/404.git
cd 404
composer install
cp .env.example .env
php artisan key:generate
vendor/bin/sail up -d
vendor/bin/sail artisan migrate
```

첫 `sail up`은 PHP 이미지를 빌드하느라 몇 분 걸린다. 두 번째부터는 몇 초.

프론트 자산:

```bash
vendor/bin/sail npm install
vendor/bin/sail npm run dev
```

## 확인

`http://localhost:8000/health`

PostgreSQL / Redis / 세션 / 큐 네 줄이 전부 초록이고, `다시 확인` 버튼을 눌러
점검 횟수가 올라가면 Livewire 왕복까지 정상이다.

## 포트

로컬에 이미 떠 있는 서비스와 부딪히지 않게 기본값에서 비켜 뒀다. `.env`에서 바꾼다.

| 용도 | 포트 |
| --- | --- |
| 앱 | 8000 |
| Vite | 5173 |
| PostgreSQL | 54320 |
| Redis | 63790 |

## 자주 쓰는 명령

```bash
vendor/bin/sail up -d          # 컨테이너 기동
vendor/bin/sail down           # 정지
vendor/bin/sail artisan ...    # artisan
vendor/bin/sail artisan test   # 테스트
vendor/bin/sail pint           # 코드 포맷
vendor/bin/sail psql           # DB 접속
```

`sail`을 매번 치기 싫으면 셸에 별칭을 둔다.

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

## 구조

로직은 `app/Services` 에 둔다. 컨트롤러와 Livewire 컴포넌트는 얇게 유지한다.
자세한 규칙은 [CLAUDE.md](CLAUDE.md).
