{{-- 확인할 문서 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-106148)
     나에게 결재가 돌아온 문서들. 전자결재의 받은함이다.

     원본 실측(1920) — 본문 1200 (x480~1680)
       제목 24 Bold lh34 + 건수 (제목 옆 29x30)
       검색·필터 카드 1200x156 · 안쪽 30
         검색 940x40 + 필터 아이콘 40 + '조회' 128x40 / 아랫줄 드롭다운 296·294 + 초기화
       알림 카드 1200x102 · 사이 16 (피치 118)

     ⚠️ 원본 검색·필터는 카드 한 장이다. 컨텐츠 관리부터 쓰기로 한 공용 x-filter-bar 로
        바꿨다 — 화면마다 필터 생김새가 갈리면 안 된다. '조회' 버튼도 없앴다.
     ⚠️ 건수는 제목 옆에 그대로 뒀다. 원본이 배지가 아니라 큰 숫자다.
     ⚠️ '결재하기' 는 아직 갈 곳이 없다. 결재 화면이 생기면 그 문서로 연결한다.
        승인·반려는 상태를 바꾸는 일이라 POST + CSRF 로 보내고 권한은 Policy 에서 본다.
     ⚠️ 값은 전부 예시다. DB 에서 오지 않는다. --}}
@php
    /*
     * 라우트에서 넘겨받은 값이 있으면 그걸 쓴다 — /documents/review-empty 가 빈 배열을 준다.
     * 퍼블릭 스페이스의 빈 화면과 같은 방식이다.
     */
    // 원본은 같은 사람 여섯 줄이라 이름만 바꿔 뒀다.
    $notices = $notices ?? [
        ['name' => '곽프로', 'doc' => '지출 결의서 · 개인 비용', 'kind' => '승인을 요청', 'time' => '1분 전', 'unread' => true],
        ['name' => '정프로', 'doc' => '지출 결의서 · 거래처', 'kind' => '업무 내용을 수정', 'time' => '12분 전', 'unread' => true],
        ['name' => '이프로', 'doc' => '근태 · 휴가 신청서', 'kind' => '승인을 요청', 'time' => '1시간 전', 'unread' => true],
        ['name' => '최프로', 'doc' => '재무 · 법인 카드 신청', 'kind' => '승인을 요청', 'time' => '3시간 전', 'unread' => false],
        ['name' => '장프로', 'doc' => '육아기 근로 시간 단축', 'kind' => '승인을 요청', 'time' => '어제', 'unread' => false],
        ['name' => '이프로', 'doc' => '근태 · 휴가 신청서', 'kind' => '승인을 요청', 'time' => '어제', 'unread' => false],
    ];
@endphp

<x-layout title="확인할 문서">
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
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[
                ['label' => '홈', 'href' => url('/workspace')],
                ['label' => '확인할 문서'],
            ]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="flex min-w-0 items-baseline gap-3 text-title-2 font-bold leading-[39px] text-mono-black">
                확인할 문서
                {{-- 비었을 땐 건수를 내지 않는다(원본 빈 화면에도 없다). '0' 이 붙어 있으면
                     세어 본 결과인지 아직 못 불러온 건지 알 수 없다. --}}
                @if ($notices)
                    <span class="tabular-nums text-label-alternative">{{ count($notices) }}</span>
                @endif
            </h1>
        </x-slot:title>

        <x-filter-bar
            search="찾으시는 문서 이름을 검색해보세요"
            :active="['requested', 'state']"
            :columns="[
                ['key' => 'requested', 'label' => '신청일 기준', 'type' => 'date'],
                ['key' => 'state', 'label' => '진행 상태', 'type' => 'select', 'options' => ['결재 진행중', '결재 완료', '반려']],
                ['key' => 'writer', 'label' => '신청자', 'type' => 'search', 'options' => ['곽프로', '정프로', '이프로', '최프로', '장프로']],
            ]"
            class="mt-8 pb-3"
        />

        {{-- 원본 카드 사이 16. 비었을 땐 카드 대신 흰 판 하나(1200x690)에 그림 + 문구다
             (node 1002-106604). --}}
        <div class="flex min-w-0 flex-col gap-4 pb-10">
            @forelse ($notices as $notice)
                <x-notice-card
                    :name="$notice['name']"
                    :message="$notice['name'] . '님이 [' . $notice['doc'] . '] ' . $notice['kind'] . '했습니다.'"
                    state="결재 진행중"
                    :time="$notice['time']"
                    :unread="$notice['unread']"
                    action="결재하기"
                />
            @empty
                {{-- 원본 판 690 · 안에서 세로 가운데. 원본 문구 끝 이모지는 빼기로 한 규칙대로
                     넣지 않았다. '성구님' 자리에는 지금 사용자 이름이 들어간다. --}}
                <div class="flex min-h-[690px] min-w-0 items-center justify-center rounded-lg bg-background-normal">
                    <x-empty-state
                        icon="empty-document"
                        title="아직 확인할 문서가 없습니다."
                        :lines="['김기안님이 확인해야 할 문서가 도착하면 이곳에서 보여드릴게요!']"
                    />
                </div>
            @endforelse
        </div>
    </x-workspace-shell>
</x-layout>
