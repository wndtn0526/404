{{-- 컨텐츠 추가 — 레이아웃은 Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-269747 "page")
     원본은 '경조 신청 추가' 폼이다. 레이아웃(가운데 792 카드 · 섹션 제목 + 2열 그리드 ·
     하단 구분선 + 취소/추가)만 가져오고, 필드는 컨텐츠 관리 표의 컬럼에서 뽑았다.

     원본 실측(1920) — 카드 792 가운데 정렬(좌우 각 564) · 반경 6 · 패딩 30
       브레드크럼 top 56 → 30 → 제목 줄(뒤로 32 + 제목 lh39) → 30 → 카드 175
       섹션 제목 20 Bold lh30 (DS heading-2 와 정확히 일치)
       제목 → 그리드 30 · 필드 칸 351 · 열 사이 30 (351+30+351 = 732 = 내부 폭)
       필드 높이 54 (라벨 12 Medium lh18 + 4 + 입력 32) · 행 사이 24 (피치 78)
       그리드 → 섹션 구분선 30 → 다음 섹션 제목 30
       마지막 그리드 → 카드 폭 구분선 40 → 25 → 버튼 120x36 (사이 16, 우측 정렬) → 30
       입력 32 · 반경 4 · 보더 Warm gray/200 · 본문 14 (DS label-1 과 정확히 일치)
       플레이스홀더 Warm gray/400 · 자동 입력 칸은 disabled

     ⚠️ 원본 섹션 구분선은 660 폭이다(내부 폭 732 보다 72 짧다). 리사이즈가 안 된 흔적으로
        보고 내부 폭에 맞췄다. 하단 구분선은 원본도 카드 폭 792 라 그대로 카드 끝까지 뺐다.
     ⚠️ 라벨이 원본은 12 Medium(Warm gray/700), DS 인풋 sm 은 14(Warm gray/800)다.
        폼 컨트롤은 DS 가 정본이라 DS 값을 썼다. 그래서 칸 높이가 54 → 58, 피치 78 → 82 다.
     ⚠️ 셀렉트는 DS 드롭다운 sm 이다. 높이 32 는 원본과 같고 본문만 13(원본 14)이다.
     ⚠️ 제목 글자는 원본이 대략 25px/lh39 다. DS 단계에 없어서 title-2(30 · lh39)를 썼다 —
        줄높이는 정확히 같고 컨텐츠 관리 목록 화면의 제목과 크기가 맞는다.
     ⚠️ 원본 제목 앞에는 이모지(📓)가 있다. 이모지는 쓰지 않기로 해서 뺐다.
     ⚠️ 저장 엔드포인트가 없다. '추가'는 아직 type=button 이라 아무 일도 하지 않는다.
        엔드포인트가 붙으면 type=submit 으로 바꾸고 form 에 @csrf 를 넣는다
        (상태를 바꾸는 요청은 POST + CSRF).
     ⚠️ 선택 목록은 컨텐츠 관리 필터 바에 넣어 둔 값과 같게 맞췄다. 실제로는 분류 테이블에서
        와야 한다 — 모델이 붙으면 컨트롤러에서 넘겨받는다(로직은 Service Layer). --}}
@php
    /*
     * 컨텐츠 관리 표의 13개 컬럼을 그대로 필드로 옮겼다.
     *   기본 설정 (필수) — 사람이 반드시 채우는 것 6개
     *   상세 설정        — 자동으로 채워지는 것 3개 + 선택 입력 4개
     * 원본도 상세 설정 쪽이 대부분 '~를 선택하면 자동 입력' 인 disabled 칸이다.
     */
    $major = ['요양보호' => '요양보호', '방문간호' => '방문간호', '치매전문' => '치매전문', '공통' => '공통'];
    $minor = ['직무향상' => '직무향상', '기록관리' => '기록관리', '의사소통' => '의사소통', '안전관리' => '안전관리', '인권보호' => '인권보호'];
    $archive = ['법정의무교육' => '법정의무교육', '전문교육' => '전문교육', '실무자료' => '실무자료'];
    $states = ['공개' => '공개', '검수중' => '검수중', '비공개' => '비공개', '반려' => '반려'];
    $years = ['2021' => '2021', '2020' => '2020', '2019' => '2019'];

    // 2열 그리드 — 원본 열 사이 30 · 행 사이 24. 좁은 화면에서는 1열로 내린다.
    $grid = 'grid grid-cols-1 gap-x-[30px] gap-y-6 sm:grid-cols-2';
@endphp

<x-layout title="컨텐츠 추가">
    <x-workspace-shell
        workspace="청담원"
        domain="cdw.workspace.io"
        user="김기안"
        has-alarm
        :rail="config('workspace.rail')"
        :items="config('workspace.items')"
        :footer-items="config('workspace.footer_items')"
        :scale="config('workspace.lnb_scale')"
    >
        {{-- 브레드크럼·제목을 셸 슬롯이 아니라 본문에 둔다. 원본이 둘 다 카드 왼쪽 끝에
             맞춰져 있어서(가운데 792 기준), 셸의 페이지 헤더(좌우 80)에 두면 어긋난다. --}}
        <div class="mx-auto w-full min-w-0 max-w-[792px]">

            <x-breadcrumb :items="[
                ['label' => '홈', 'href' => url('/workspace')],
                ['label' => '컨텐츠 관리', 'href' => url('/contents')],
                ['label' => '컨텐츠 추가'],
            ]" />

            {{-- 뒤로 + 제목 — 원본은 화살표 32 와 제목이 서로 가운데 정렬돼 있다 --}}
            <div class="flex min-w-0 items-center gap-4 pt-[30px]">
                <a href="{{ url('/contents') }}"
                   class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-label-normal transition-colors hover:bg-warm-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                   aria-label="컨텐츠 관리로 돌아가기">
                    <x-icon-arrow-left class="size-6" />
                </a>
                <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">컨텐츠 추가</h1>
            </div>

            <form method="POST" action="#" class="mt-[30px] min-w-0 rounded-lg bg-background-normal p-5 lg:p-[30px]">
                {{-- 엔드포인트가 붙을 때 @csrf 를 여기 둔다. --}}

                {{-- ═══ 기본 설정 (필수) ═══ --}}
                <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">기본 설정 (필수)</h2>

                {{-- 필드마다 required 를 걸지 않는다. 원본은 필수를 섹션 제목에서 한 번만
                     알리고 칸에는 별표를 붙이지 않는다. 섹션이 (필수)인데 일부에만 별표가
                     붙으면 나머지는 안 채워도 되는 것처럼 읽힌다.
                     실제 검증 규칙은 저장이 붙을 때 FormRequest·서비스에 둔다. --}}
                <div class="{{ $grid }} pt-[30px]">
                    {{-- 영상 파일 — 이 화면이 등록하는 실체다. 두 열을 다 쓴다.
                         multiple 을 주지 않았으므로 한 편만 올라간다(고르면 앞의 것이 교체된다).
                         원본(node 1002-269747)에는 업로드 칸이 없다. DS 드롭존도 원본 밖 확장이다.
                         ⚠️ 아래 '영상 분·초' 는 원래 이 파일에서 읽어야 하는 값이다. 원본 폼의
                            '~를 선택하면 자동 입력' 칸들과 같은 성격이라, 업로드 처리가 붙으면
                            disabled 자동 입력으로 바꾸는 게 맞다. 지금은 손입력으로 뒀다.
                         ⚠️ 업로드 엔드포인트가 없다. 파일을 골라도 화면에 칩만 뜬다. 실제로는
                            공개 디스크에 두지 않고 권한 확인 후 스트리밍한다(CLAUDE.md). --}}
                    <x-file-dropzone
                        class="sm:col-span-2"
                        label="영상 파일"
                        name="video"
                        accept="video/mp4,video/quicktime,video/x-m4v,.mp4,.mov,.m4v"
                        title="영상 파일을 끌어다 놓거나 클릭해 업로드 해주세요."
                        hint="파일 형식은 MP4 · MOV로 업로드 해주세요."
                        action="영상 선택"
                    />

                    <x-input label="제목" name="title" size="sm" placeholder="컨텐츠 제목 입력" />
                    <x-dropdown label="상태" name="state" size="sm" :options="$states" selected="검수중" />

                    <x-dropdown label="대분류" name="major" size="sm" :options="$major" placeholder="대분류 선택" />
                    <x-dropdown label="중분류" name="minor" size="sm" :options="$minor" placeholder="중분류 선택" />

                    {{-- 소분류는 필터 바에서도 자유 입력이다(분류 테이블이 없다) --}}
                    <x-input label="소분류" name="sub" size="sm" placeholder="소분류 입력" />
                    <x-dropdown label="아카이브 분류" name="archive" size="sm" :options="$archive" placeholder="아카이브 분류 선택" />
                </div>

                {{-- 섹션 구분선 — 카드 내부 폭 --}}
                <div class="mt-[30px] h-px bg-warm-gray-100" aria-hidden="true"></div>

                {{-- ═══ 상세 설정 ═══ --}}
                <h2 class="pt-[30px] text-heading-2 font-bold leading-[30px] text-mono-black">상세 설정</h2>

                <div class="{{ $grid }} pt-[30px]">
                    {{-- 자동 부여 3개 — 저장할 때 서버가 채운다. 원본도 이런 칸은 disabled 다. --}}
                    <x-input label="컨텐츠ID" size="sm" value="저장하면 자동 발급" disabled />
                    <x-input label="등록자" size="sm" value="김기안" disabled />

                    <x-input label="등록일" size="sm" value="저장하면 자동 기록" disabled />
                    <x-dropdown label="제작연도" name="year" size="sm" :options="$years" placeholder="제작연도 선택" />

                    {{-- 영상 길이 — 표에서 '00분 / 00초' 로 보여준다. 값이 없으면 하이픈이다. --}}
                    <x-input label="영상 분" name="minutes" type="number" size="sm" min="0" max="999" placeholder="0" />
                    <x-input label="영상 초" name="seconds" type="number" size="sm" min="0" max="59" placeholder="0" />

                    <x-input label="태그명" name="tags" size="sm" placeholder="쉼표로 구분해서 입력" />
                </div>

                {{-- 하단 구분선 — 원본은 카드 폭(792) 전체를 지난다. 패딩 밖으로 뺀다. --}}
                <div class="-mx-5 mt-10 h-px bg-warm-gray-100 lg:-mx-[30px]" aria-hidden="true"></div>

                <div class="flex flex-wrap items-center justify-end gap-4 pt-[25px]">
                    <x-button variant="outline" size="sm" href="{{ url('/contents') }}" class="w-[120px]">취소</x-button>
                    {{-- 엔드포인트가 붙으면 type=submit 으로 바꾼다 --}}
                    <x-button variant="primary" size="sm" type="button" class="w-[120px]">추가</x-button>
                </div>
            </form>
        </div>
    </x-workspace-shell>
</x-layout>
