{{-- 지출 결의서 (개인 비용) 의 '기본 내용' + '상세 내용' — Figma GPRO_PORTFOLIO
     빈 화면 node 1002-113826 · 다 채운 화면 node 1002-115013.
     documents-create.blade.php 가 양식에 따라 이 조각이나 doc-form-vacation 을 부른다.
     바깥 껍데기(머리·관련 파일·참조 문서·결재선·팝업)는 양식이 달라도 같다. --}}
    {{-- ── 기본 내용 ── --}}
    <section class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">기본 내용</h2>

        <div class="pt-[18px]">
            <x-input label="문서 이름" name="doc_name" size="sm"
                     placeholder="지출 결의서 (개인 비용)" x-model="form.name" />
        </div>

        <div class="grid grid-cols-1 gap-x-6 gap-y-6 pt-6 sm:grid-cols-2">
            <x-dropdown label="귀속년월" name="doc_month" size="sm"
                        :options="['2021.10' => '2021.10', '2021.09' => '2021.09', '2021.08' => '2021.08']"
                        selected="2021.10" x-model="form.month" />

            {{-- 계좌 정보 — 원본대로 테두리 하나 안에 은행 + 구분선 + 계좌번호.
                 칸은 x-field-group 이 그리고, 안쪽은 DS 컨트롤을 bare 로 넣는다.
                 컨트롤을 직접 만들지 않으면서 원본 모양을 낸다. --}}
            <x-field-group label="계좌 정보" size="sm" for="doc_bank">
                <div class="w-[124px] shrink-0">
                    <x-dropdown id="doc_bank" name="doc_bank" variant="bare" size="sm"
                                placeholder="은행 선택"
                                {{-- 'GPRO 뱅크' 는 원본이 쓰는 가상의 은행이다. 다 채운 화면이
                                     고르는 값이라 목록에 있어야 빈칸으로 떨어지지 않는다. --}}
                                :options="['GPRO 뱅크' => 'GPRO 뱅크', '국민은행' => '국민은행', '신한은행' => '신한은행', '하나은행' => '하나은행', '우리은행' => '우리은행']"
                                :selected="$initForm['bank'] ?? null"
                                x-model="form.bank" />
                </div>
                <div class="min-w-0 flex-1">
                    <x-input name="doc_account" variant="bare" size="sm"
                             placeholder="계좌 번호 입력" x-model="form.account" />
                </div>
            </x-field-group>
        </div>

        {{-- 상세 내용이 하나라도 있으면 원본(node 1002-115013)처럼 제목 + 표가 선다.
             하나도 없으면 아래 '+ 상세 내용 추가' 줄만 남는다(node 1002-113826).

             원본 실측 — 제목은 계좌 정보 줄에서 40 아래 · 표는 제목에서 20 아래
               표가 카드 안쪽 폭(792)을 꽉 채운다. 카드 패딩 30 을 음수 여백으로 되돌리고
               첫 칸만 좌 패딩 30 을 줘서 제목과 세로줄을 맞춘다.
               열 7개 120/110/120/100/114/100/128 · 머리 56 · 줄 56 · 마지막 줄 28 --}}
        <template x-if="details.length">
            <div>
                <h2 class="{{ $cardTitle }} pt-10">상세 내용</h2>

                <div class="-mx-[30px] mt-5">
                    <x-table flush min-width="792px"
                             class="[&_td:first-child]:pl-[30px] [&_th:first-child]:pl-[30px]">
                        {{-- 열 이름·차례는 원본 그대로다. 팝업에서 넣을 때 본 이름과
                             쌓인 뒤 보는 이름이 달라지면 안 된다. --}}
                        <x-table.head :columns="[
                            ['label' => '프로젝트', 'width' => '120px'],
                            ['label' => '비용 분류', 'width' => '110px'],
                            ['label' => '비용 내역', 'width' => '120px'],
                            ['label' => '사용 날짜', 'width' => '100px'],
                            ['label' => '상세 내역', 'width' => '114px'],
                            ['label' => '거래처 이름', 'width' => '100px'],
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
                                    <x-table.cell tone="muted" nowrap><span x-text="d.vendor"></span></x-table.cell>
                                    {{-- ⚠️ 원본에는 삭제 자리가 없다(열 7개로 792 가 딱 맞는다). 그렇다고 잘못
                                         넣은 줄을 지울 방법이 없으면 곤란해서, 열을 늘리지 않고 마지막 칸
                                         오른쪽에 겹쳐 뒀다. 줄에 마우스를 올리거나 탭으로 짚어야 나온다. --}}
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

                            {{-- 표 맨 아래 28 줄이 '+ 내역 추가' 다. 원본은 잠긴 줄(Row Locked)
                                 모양이라 글자가 13 Warm gray/400 이다.
                                 ⚠️ 마지막 줄이라도 아래 선을 남긴다 — 원본에 있다. --}}
                            <x-table.row :hover="false" class="border-b! border-line-solid-normal!">
                                <x-table.cell dense colspan="7" class="p-0!">
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

        {{-- 아직 아무것도 없을 때만 나오는 줄. 표가 서면 '+ 내역 추가' 가 그 자리를 대신한다. --}}
        <button type="button" class="{{ $addRow }} mt-6" x-cloak
                x-bind:class="details.length ? 'hidden' : 'inline-flex'"
                @click="$dispatch('open-modal', 'detail-add')">
            <x-icon-square-plus class="size-6 shrink-0" />
            상세 내용 추가
        </button>
    </section>
