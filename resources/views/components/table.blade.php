@props([
    'headers' => [],
])

<div class="w-full overflow-x-auto custom-scrollbar">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-xs sm:text-sm border-collapse']) }}>
        @if(isset($header) || count($headers) > 0)
            <thead>
                <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80 select-none">
                    @if(isset($header))
                        {{ $header }}
                    @else
                        @foreach($headers as $h)
                            <th class="px-4 py-3.5 font-bold">{{ $h }}</th>
                        @endforeach
                    @endif
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-slate-100 bg-white">
            {{ $slot }}
        </tbody>
        @if(isset($footer))
            <tfoot>
                {{ $footer }}
            </tfoot>
        @endif
    </table>
</div>
