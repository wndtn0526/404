{{-- DS Table — 백오피스(관리자) 데이터 테이블 루트. (Figma 99:17983)
     실제 <table> 시맨틱 + 가로 스크롤(좁은 화면). 헤더/행/셀은 <x-table.head|row|cell>로 조합.
     · 헤더: 회색 면(bg-background-alternative) + 13px 라벨
     · 선택: selectable 추가 시 체크박스 컬럼(전체선택/행선택) · 선택행 핑크 하이라이트
     예)
       <x-table min-width="1040px" selectable>
         <x-table.head selectable :all-ids="$ids" :columns="['이름','연락처',['label'=>'상태','width'=>'100px']]" />
         <tbody>
           @foreach ($rows as $r)
             <x-table.row selectable :value="$r['id']">
               <x-table.cell tone="strong" nowrap>{{ $r['name'] }}</x-table.cell>
               <x-table.cell tone="muted" nowrap>{{ $r['phone'] }}</x-table.cell>
               <x-table.cell><x-badge variant="outlined">{{ $r['status'] }}</x-badge></x-table.cell>
             </x-table.row>
           @endforeach
         </tbody>
       </x-table> --}}
@props([
    'minWidth' => '640px',   // 칼럼이 많을 때 최소 너비(이하로 좁아지면 가로 스크롤)
    'selectable' => false,   // 체크박스 선택 컬럼 사용(Alpine `selected` 배열 제공)
    // flush: 바깥 테두리·모서리를 없앤다. 카드 안에 폭을 꽉 채워 넣을 때 쓴다.
    // ⚠️ 원본 Data Tables 는 테두리가 있지만 기안 작성의 '상세 내용' 표(node 1002:115066)는
    //    카드 안쪽 폭 792 를 그대로 채우고 테두리가 없다. 카드가 이미 면을 나누고 있어서다.
    'flush' => false,
])

<div {{ $attributes->class([
        'w-full overflow-x-auto bg-background-normal',
        'rounded-xs border border-line-solid-neutral' => ! $flush,
    ]) }}
     @if ($selectable) x-data="{ selected: [] }" @endif>
    <table class="w-full border-collapse text-left" style="min-width: {{ $minWidth }}">
        {{ $slot }}
    </table>
</div>
