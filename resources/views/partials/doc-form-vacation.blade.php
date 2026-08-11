{{-- 휴가 신청의 '안내' + '신청 내용' — Figma GPRO_PORTFOLIO (node 1002-108468)
       빈 화면 1002-108470 (신청 내용 792x316) · 다 채운 화면 1002-108556 (792x417)
       종류별 변형 1002-108519 · 108529 · 108539

     원본 실측
       안내 카드 792x134 · 안쪽 30
         제목 20 Bold lh30 — 남은 일수만 Primary/green 900
         값 줄 제목에서 24 아래 · 라벨 12→14 Medium Warm gray/500 · 값 14 Medium 검정 · 사이 16
         두 쌍이 한 줄에 (왼쪽 0 · 오른쪽 379)
       신청 내용 카드 792x316(빈) → 417(날짜 칸이 생기면)
         문서 이름 732x54 · 법인 354 + 휴가 종류 354 · [사용할 날짜 354 (+ 사용할 시간 354)] · 신청 사유 732
         날짜/시간 칸은 55 다 — 입력 32 아래에 요약 줄(체크 12 + 글자 12 Primary/green 900)이 붙는다

     원본 주석(node 1002-108552) 그대로
       "신청하는 휴가 종류에 따라 '사용할 날짜', '사용할 시간' 등의 인풋 박스가 맞춰 등장합니다.
        그리고 시간 정보를 입력하면 박스 하단에 총 시간과 날짜가 노출되어 사용자가 더블 체크할 수 있습니다."

     ⚠️ 라벨을 '휴가 종류' 로 뒀다. 다 채운 화면(1002-108556)만 '신청 구분' 이고 빈 화면과
        종류별 변형 셋은 모두 '휴가 종류' 라 다수를 따랐다. 디자이너 확인이 필요하다.
     ⚠️ 원본 필드 라벨은 12 Medium Warm gray/700 인데 이 저장소 x-input 은 14 다
        (component 주석에 '시니어 가독성' 이라고 이유가 적혀 있다). 여기만 12 로 내리면
        다른 화면과 어긋나서 DS 를 따랐다. 폼 라벨 크기는 따로 정할 문제다.
     ⚠️ '사용할 날짜'·'사용할 시간' 은 원본이 캐럿 달린 칸이라 x-dropdown 으로 냈다.
        DS x-datepicker 는 하루짜리에 높이도 40 이상이라 이 자리에 맞지 않는다.
        실제로 붙일 때는 기간 선택기가 필요하다.
     ⚠️ 법인은 못 고치는 값이라 비활성 입력이다. 값은 예시다. --}}

{{-- ── 안내 792x134 ── --}}
<section class="{{ $card }}">
    <h2 class="{{ $cardTitle }}">
        사용할 수 있는 휴가는 <span class="text-status-positive" x-text="leaveLeft() + '일'">7일</span>입니다.
    </h2>

    <dl class="grid grid-cols-1 gap-x-6 gap-y-2 pt-6 sm:grid-cols-2">
        <div class="flex min-w-0 items-center gap-4">
            <dt class="shrink-0 text-label-1 font-medium leading-5 text-label-alternative">전체 휴가</dt>
            <dd class="min-w-0 text-label-1 font-medium leading-5 text-mono-black tabular-nums"
                x-text="leave.total + '일'">15일</dd>
        </div>
        <div class="flex min-w-0 items-center gap-4">
            <dt class="shrink-0 text-label-1 font-medium leading-5 text-label-alternative">사용한 휴가</dt>
            <dd class="min-w-0 text-label-1 font-medium leading-5 text-mono-black tabular-nums"
                x-text="leave.used + '일'">8일</dd>
        </div>
    </dl>
</section>

{{-- ── 신청 내용 792x316~417 ── --}}
<section class="{{ $card }}">
    <h2 class="{{ $cardTitle }}">신청 내용</h2>

    <div class="pt-[18px]">
        <x-input label="문서 이름" name="doc_name" size="sm" placeholder="휴가 신청" x-model="form.name" />
    </div>

    <div class="grid grid-cols-1 gap-x-6 gap-y-6 pt-6 sm:grid-cols-2">
        {{-- 법인 — 못 고치는 값이다. 비활성 입력의 회색 면이 그걸 말해 준다. --}}
        <x-input label="법인" name="doc_company" size="sm" value="GPRO 그룹" disabled />

        <x-dropdown label="휴가 종류" name="doc_leave_type" size="sm" placeholder="선택"
                    :options="[
                        '연차' => '연차',
                        '오전 반차' => '오전 반차',
                        '오후 반차' => '오후 반차',
                        '경조' => '경조',
                        '훈련 (민방위)' => '훈련 (민방위)',
                        '병가' => '병가',
                    ]"
                    :selected="$initForm['leave_type'] ?? null"
                    x-model="form.leave_type" />
    </div>

    {{-- 종류를 고르기 전에는 이 줄이 없다. 원본 빈 화면(316)에 날짜 칸이 없는 이유다. --}}
    <template x-if="form.leave_type">
        <div class="grid grid-cols-1 gap-x-6 gap-y-6 pt-6 sm:grid-cols-2">
            <div class="min-w-0">
                <x-dropdown label="사용할 날짜" name="doc_leave_date" size="sm" placeholder="날짜 선택"
                            :options="[
                                '2021.10.01' => '2021.10.01',
                                '2021.10.01 - 2021.10.01' => '2021.10.01 - 2021.10.01',
                                '2021.10.01 - 2021.10.05' => '2021.10.01 - 2021.10.05',
                            ]"
                            :selected="$initForm['leave_date'] ?? null"
                            x-model="form.leave_date" />
                @include('partials.doc-form-vacation-summary', ['expr' => 'dateSummary()'])
            </div>

            {{-- 시간을 받는 종류에서만 한 칸 더 선다(원본 node 1002-108539). --}}
            <template x-if="needsTime()">
                <div class="min-w-0">
                    <x-dropdown label="사용할 시간" name="doc_leave_time" size="sm" placeholder="시간 선택"
                                :options="[
                                    '09:00 - 13:00' => '09:00 - 13:00',
                                    '10:00 - 18:00' => '10:00 - 18:00',
                                    '13:00 - 18:00' => '13:00 - 18:00',
                                ]"
                                :selected="$initForm['leave_time'] ?? null"
                                x-model="form.leave_time" />
                    @include('partials.doc-form-vacation-summary', ['expr' => 'timeSummary()'])
                </div>
            </template>
        </div>
    </template>

    <div class="pt-6">
        <x-input label="신청 사유" name="doc_reason" size="sm" placeholder="내용 입력" x-model="form.reason" />
    </div>
</section>
