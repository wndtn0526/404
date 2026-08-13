{{-- 지출 결의서 (거래처) 의 '기본 내용' + '상세 내용' — Figma GPRO_PORTFOLIO
       빈 화면 node 1002-108416 (기본 내용 792x545)
       다 채운 화면 node 1002-108333 (기본 내용 792x805)

     개인 비용과 뼈대는 같고 받는 값이 다르다.
       귀속처 · 예금주 · 파트너사 · 사업자 번호 가 늘고, 상세 내용 표에서 거래처 열이 빠진다.
       (거래처는 문서 단위로 정해지니 줄마다 다시 받지 않는다)
       카드 아래에 구분선 + '빠른 처리가 필요한 긴급 문서인가요?' 토글이 붙는다.

     원본 실측
       문서 이름 732 / 귀속처 354 + 귀속 년월 354 / 계좌 정보 354 + 예금주 354
       / 파트너사 354 + 사업자 번호 354 / (상세 내용 표) / 구분선 732x1 / 토글 40x22
       / 지급 요청일 354 + 결제 금액 합계 354
       표 열 6개 — 프로젝트 120 · 비용 분류 110 · 비용 내역 120 · 사용 날짜 100 ·
                  상세 내역 214 · 사용 금액 128 (개인 비용의 7열에서 '거래처 이름' 이 빠졌다)

     ⚠️ '지급 요청일'·'결제 금액 합계' 는 토글을 켜야 나오는 것으로 봤다. 빈 화면(토글 꺼짐)에는
        두 칸이 없고 다 채운 화면(토글 켜짐)에만 있다. 원본에 설명 글이 없어 추정이다 —
        상세 내용이 생기면 나오는 것일 수도 있다. 디자이너 확인이 필요하다.
     ⚠️ '사업자 번호' 는 파트너사를 고르면 따라오는 값이라 비활성으로 뒀다. 원본도 회색 면이다.
     ⚠️ '결제 금액 합계' 는 상세 내용의 사용 금액을 더해서 낸다. 원본 값(100,000)은 표의
        3,000,000 과 맞지 않는 예시값이다 — 더해서 내는 쪽이 맞다고 보고 그렇게 했다.
     ⚠️ 계좌 번호·사업자 번호는 개인정보·거래정보다. 지금은 화면뿐이지만 실제로 붙을 때
        평문 저장·평문 로그를 하지 않는다(CLAUDE.md). 저장 전에 담당자 확인부터. --}}

<section class="{{ $card }}">
    <h2 class="{{ $cardTitle }}">기본 내용</h2>

    <div class="pt-[18px]">
        <x-input label="문서 이름" name="doc_name" size="sm"
                 placeholder="지출 결의서 · 거래처" x-model="form.name" />
    </div>

    <div class="grid grid-cols-1 gap-x-6 gap-y-6 pt-6 sm:grid-cols-2">
        <x-dropdown label="귀속처" name="doc_org" size="sm" placeholder="선택"
                    :options="['뉴 게임즈' => '뉴 게임즈', '청담원' => '청담원', 'GPRO 그룹' => 'GPRO 그룹']"
                    :selected="$initForm['org'] ?? null" x-model="form.org" />

        <x-dropdown label="귀속 년월" name="doc_month" size="sm"
                    :options="['2021.08' => '2021.08', '2021.09' => '2021.09', '2021.10' => '2021.10']"
                    :selected="$initForm['month'] ?? null" x-model="form.month" />
    </div>

    <div class="grid grid-cols-1 gap-x-6 gap-y-6 pt-6 sm:grid-cols-2">
        {{-- 계좌 정보 — 개인 비용과 같은 복합 칸이다. 칸은 x-field-group 이 그리고
             안쪽은 DS 컨트롤을 bare 로 넣는다. --}}
        <x-field-group label="계좌 정보" size="sm" for="doc_bank">
            <div class="w-[124px] shrink-0">
                <x-dropdown id="doc_bank" name="doc_bank" variant="bare" size="sm" placeholder="은행 선택"
                            :options="['GPRO 뱅크' => 'GPRO 뱅크', '국민은행' => '국민은행', '신한은행' => '신한은행', '하나은행' => '하나은행', '우리은행' => '우리은행']"
                            :selected="$initForm['bank'] ?? null" x-model="form.bank" />
            </div>
            <div class="min-w-0 flex-1">
                <x-input name="doc_account" variant="bare" size="sm"
                         placeholder="계좌 번호 입력" x-model="form.account" />
            </div>
        </x-field-group>

        <x-input label="예금주" name="doc_holder" size="sm"
                 placeholder="예금주 이름 입력" x-model="form.holder" />
    </div>

    <div class="grid grid-cols-1 gap-x-6 gap-y-6 pt-6 sm:grid-cols-2">
        <x-input label="파트너사" name="doc_partner" size="sm"
                 placeholder="파트너사 선택" x-model="form.partner" />

        {{-- 파트너사를 고르면 따라오는 값이라 못 고친다 --}}
        <x-input label="사업자 번호" name="doc_biz_no" size="sm"
                 placeholder="파트너사를 고르면 채워집니다" :value="$initForm['biz_no'] ?? null" disabled />
    </div>

    {{-- 상세 내용 — 개인 비용과 같은 모양이고 열만 하나 적다.
         카드 안쪽 폭을 꽉 채우고, 표 맨 아래 28 줄이 '+ 내역 추가' 다. --}}
    <template x-if="details.length">
        <div>
            <h2 class="{{ $cardTitle }} pt-10">상세 내용</h2>

            <div class="-mx-[30px] mt-5">
                <x-table flush min-width="792px"
                         class="[&_td:first-child]:pl-[30px] [&_th:first-child]:pl-[30px]">
                    <x-table.head :columns="[
                        ['label' => '프로젝트', 'width' => '120px'],
                        ['label' => '비용 분류', 'width' => '110px'],
                        ['label' => '비용 내역', 'width' => '120px'],
                        ['label' => '사용 날짜', 'width' => '100px'],
                        ['label' => '상세 내역', 'width' => '214px'],
                        ['label' => '사용 금액', 'width' => '128px'],
                    ]" />
                    <tbody>
                        <template x-for="(d, i) in details" :key="i">
                            <x-table.row class="group/row">
                                <x-table.cell tone="muted" nowrap><span x-text="d.project"></span></x-table.cell>
                                <x-table.cell tone="muted" nowrap><span x-text="d.category"></span></x-table.cell>
                                <x-table.cell tone="muted" nowrap><span x-text="d.account"></span></x-table.cell>
                                <x-table.cell tone="muted" nowrap><span class="tabular-nums" x-text="d.used_at"></span></x-table.cell>
                                <x-table.cell tone="muted" nowrap><span x-text="d.memo"></span></x-table.cell>
                                {{-- ⚠️ 원본에 삭제 자리가 없다. 열을 늘리지 않고 마지막 칸 오른쪽에 겹쳐 뒀다 —
                                     개인 비용과 같은 방식이다. 줄에 올리거나 탭으로 짚어야 나온다. --}}
                                <x-table.cell tone="muted" nowrap class="relative">
                                    <span class="tabular-nums" x-text="d.amount"></span>
                                    <button type="button" @click="details.splice(i, 1)"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 rounded-xs bg-background-normal p-0.5 text-label-alternative opacity-0 transition group-hover/row:opacity-100 hover:text-label-normal focus:opacity-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                                            aria-label="상세 내용 삭제">
                                        <x-icon-close class="size-[18px]" />
                                    </button>
                                </x-table.cell>
                            </x-table.row>
                        </template>

                        <x-table.row :hover="false" class="border-b! border-line-solid-normal!">
                            <x-table.cell dense colspan="6" class="p-0!">
                                <button type="button" @click="$dispatch('open-modal', 'detail-add')"
                                        class="flex h-7 w-full items-center pl-[30px] pr-4 text-left text-label-2 text-label-assistive transition-colors hover:text-label-normal focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                                    + 내역 추가
                                </button>
                            </x-table.cell>
                        </x-table.row>
                    </tbody>
                </x-table>
            </div>
        </div>
    </template>

    <button type="button" class="{{ $addRow }} mt-6" x-cloak
            x-bind:class="details.length ? 'hidden' : 'inline-flex'"
            @click="$dispatch('open-modal', 'detail-add')">
        <x-icon-square-plus class="size-6 shrink-0" />
        상세 내용 추가
    </button>

    {{-- 구분선 732x1 — 상세 내용과 긴급/지급 칸을 가른다 --}}
    <div class="mt-8 h-px bg-line-solid-neutral"></div>

    {{-- 원본 토글은 40x22 다. DS switch sm 이 40x24 로 가장 가깝다.
         글자가 왼쪽, 토글이 오른쪽이라 x-switch 의 label 슬롯을 쓰지 않고 옆에 두었다. --}}
    <div class="mt-8 flex min-w-0 items-center gap-4">
        <span class="text-label-1 leading-5 text-mono-black">빠른 처리가 필요한 긴급 문서인가요?</span>
        <x-switch size="sm" name="doc_urgent" x-model="form.urgent" />
    </div>

    {{-- 긴급이면 언제까지 지급해야 하는지 받는다. 합계는 상세 내용에서 더해 낸다. --}}
    <template x-if="form.urgent">
        <div class="grid grid-cols-1 gap-x-6 gap-y-6 pt-6 sm:grid-cols-2">
            <x-dropdown label="지급 요청일" name="doc_pay_due" size="sm" placeholder="날짜 선택"
                        :options="['2021.08.30' => '2021.08.30', '2021.09.10' => '2021.09.10', '2021.09.30' => '2021.09.30']"
                        :selected="$initForm['pay_due'] ?? null" x-model="form.pay_due" />

            {{-- 상세 내용에서 더해 내는 값이라 못 고친다. 칸은 DS 입력을 그대로 쓴다. --}}
            <x-input label="결제 금액 합계" name="doc_pay_total" size="sm" disabled
                     x-bind:value="amountTotal()" />
        </div>
    </template>
</section>
