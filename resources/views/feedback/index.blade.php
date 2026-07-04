<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full print:hidden">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">
                    System Feedback
                </h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Encrypted User Logs</span>
            </div>
            
            <a href="https://docs.google.com/forms/d/e/1FAIpQLSft1kJw5okdZhsNdbzbausjDg7lRXpVJPdVmtxLsdHsUkisUw/viewform?usp=publish-editor" target="_blank" class="bg-[#22a0f6] hover:bg-blue-600 text-white px-4 py-1.5 rounded-sm text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-1.5 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Open Google Form
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 print:hidden">
            <div class="bg-white border border-slate-200 p-4 flex flex-col justify-between shadow-sm">
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-2">Total Incoming Logs</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-slate-800 tabular-nums">{{ sprintf('%02d', $feedbacks->count()) }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase">Signals</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 p-4 flex flex-col justify-between shadow-sm">
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-2">Security & Bug Matrix</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-rose-600 tabular-nums">
                        {{ sprintf('%02d', $feedbacks->where('kategori', 'Bug')->count() + $feedbacks->where('kategori', 'bug')->count()) }}
                    </span>
                    <span class="text-[9px] font-black text-rose-500 bg-rose-50 px-1.5 py-0.5 uppercase tracking-tighter">Patch Required</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 p-4 flex flex-col justify-between shadow-sm">
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-2">Feature Optimization</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-blue-500 tabular-nums">
                        {{ sprintf('%02d', $feedbacks->where('kategori', 'Saran')->count() + $feedbacks->where('kategori', 'saran')->count()) }}
                    </span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase">Saran</span>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 p-4 flex flex-col justify-between shadow-sm text-white">
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-2">Identity Shield Status</span>
                <div class="text-[11px] font-black text-emerald-400 font-mono tracking-wider uppercase flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                    ANONYMOUS SHIELD ON
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-none shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-widest italic">User Feedback Matrix Sheet</h3>
                <span class="text-[9px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-2 py-0.5 uppercase tracking-wider">LIVE DATA FEED</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-400">
                            <th class="w-[8%] px-5 py-3 text-[9px] font-black uppercase tracking-widest text-center">No.</th>
                            <th class="w-[22%] px-5 py-3 text-[9px] font-black uppercase tracking-widest">Profile Signature</th>
                            <th class="w-[12%] px-5 py-3 text-[9px] font-black uppercase tracking-widest text-center">Category</th>
                            <th class="w-[43%] px-5 py-3 text-[9px] font-black uppercase tracking-widest">Message Payload / Description</th>
                            <th class="w-[15%] px-5 py-3 text-right text-[9px] font-black uppercase tracking-widest">Received At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($feedbacks as $index => $f)
                        <tr class="hover:bg-slate-50/40 transition-colors group">
                            <td class="px-5 py-4 text-[11px] font-black text-slate-400 text-center font-mono align-top">
                                {{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="px-5 py-4 align-top">
                                <div class="text-[11px] font-black text-slate-800 uppercase tracking-tight flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    NO_NAME_ENTRY
                                </div>
                                <div class="text-[8px] text-slate-400 font-mono tracking-widest uppercase mt-0.5">
                                    SIGN_ID: [ANON_DEPL_LOG_{{ $f->id }}]
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center align-top">
                                @php
                                    $kat = strtolower($f->kategori);
                                    $badgeStyle = "bg-slate-50 border-slate-200 text-slate-600"; 
                                    if ($kat === 'bug' || $kat === 'error') {
                                        $badgeStyle = "bg-rose-50 border-rose-100 text-rose-600 font-black";
                                    } elseif ($kat === 'saran' || $kat === 'idea') {
                                        $badgeStyle = "bg-blue-50 border-blue-100 text-[#22a0f6] font-black";
                                    } elseif ($kat === 'performa' || $kat === 'speed') {
                                        $badgeStyle = "bg-amber-50 border-amber-100 text-amber-600 font-black";
                                    }
                                @endphp
                                <span class="inline-block px-2 py-0.5 border text-[8px] uppercase tracking-wider rounded-none {{ $badgeStyle }}">
                                    {{ $f->kategori ?? 'GENERAL' }}
                                </span>
                            </td>

                            <td class="px-5 py-4 align-top">
                                <div class="text-[11px] font-medium text-slate-600 leading-relaxed break-words whitespace-pre-line">
                                    {{ $f->pesan ?? $f->isi_feedback ?? '-' }}
                                </div>
                            </td>

                            <td class="px-5 py-4 text-right align-top text-slate-400 font-mono text-[10px] tabular-nums">
                                @if($f->created_at)
                                    <div>{{ $f->created_at->format('Y-m-d') }}</div>
                                    <div class="text-[8px] font-bold text-slate-300 uppercase tracking-tighter mt-0.5">{{ $f->created_at->format('H:i:s') }}</div>
                                @else
                                    {{ date('Y-m-d') }}
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-20 text-center text-[10px] font-black text-slate-300 uppercase tracking-[0.5em]">
                                No Incoming User Feedback Logs Found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>