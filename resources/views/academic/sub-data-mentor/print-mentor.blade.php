<!-- ========================================================================= -->
<!-- DOKUMEN CETAK FORMAL & REKAPITULASI (KHUSUS MODE PRINT)                   -->
<!-- ========================================================================= -->
<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 12mm 10mm;
        }
        aside, header, footer, nav, .print\:hidden, #global-alert-container, button {
            display: none !important;
        }
        body, html, main, #main-content-container, .flex-1, .h-screen, .overflow-hidden, .overflow-y-auto {
            border: none !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            height: auto !important;
            width: 100% !important;
            max-width: 100% !important;
            background: #ffffff !important;
            box-shadow: none !important;
        }
        .layout-cetak-mentor {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .print-group-program {
            page-break-after: always !important;
            break-after: page !important;
            display: block !important;
            margin-bottom: 0 !important;
            padding-bottom: 2rem !important;
        }
        .print-group-program:last-of-type {
            page-break-after: auto !important;
            break-after: auto !important;
        }
        .print-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .print-table th, .print-table td {
            border: 1px solid #475569 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="hidden print:block text-black w-full bg-white layout-cetak-mentor">
    @php 
        $groupedMentors = collect($trainers ?? [])->groupBy(fn($item) => $item->program_pelatihan ?: 'UMUM'); 
    @endphp

    @foreach ($groupedMentors as $program => $listMentor)
        <div class="print-group-program" data-program="{{ strtoupper($program) }}">
            <div class="border-b-2 border-slate-900 pb-2 mb-3 flex justify-between items-end">
                <div>
                    <h1 class="text-base font-black uppercase tracking-tight text-slate-900 leading-none">LAPORAN REKAPITULASI DATA MENTOR</h1>
                    <p class="text-[9px] font-bold text-slate-600 uppercase tracking-widest mt-1">PT MILI TALENTA INSPIRASI • PORTAL AKADEMIK</p>
                </div>
                <div class="text-right">
                    <span class="text-[8.5px] font-mono font-bold text-slate-500 uppercase">TANGGAL CETAK: {{ date('d-m-Y H:i') }} WIB</span>
                </div>
            </div>

            <div class="bg-slate-100 px-3 py-1.5 border border-slate-400 mb-2 flex justify-between items-center">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-900">BIDANG PENGAMPU: {{ $program }}</span>
                <span class="text-[9px] font-bold font-mono text-slate-700 uppercase print-subtotal-text">SUBTOTAL: {{ count($listMentor) }} SDM</span>
            </div>

            <table class="w-full text-left border-collapse print-table">
                <thead>
                    <tr class="bg-slate-100 text-slate-900">
                        <th class="px-2 py-1.5 text-[9px] font-black uppercase w-[7%] text-center">NO.</th>
                        <th class="px-2.5 py-1.5 text-[9px] font-black uppercase w-[38%]">NAMA MENTOR</th>
                        <th class="px-2.5 py-1.5 text-[9px] font-black uppercase w-[28%]">EMAIL</th>
                        <th class="px-2.5 py-1.5 text-[9px] font-black uppercase w-[15%] text-center">KONTAK</th>
                        <th class="px-2.5 py-1.5 text-[9px] font-black uppercase w-[12%] text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listMentor as $subIndex => $m)
                        <tr class="row-print-target" 
                            data-program="{{ strtoupper($m->program_pelatihan ?: 'UMUM') }}" 
                            data-name="{{ strtoupper($m->nama_mentor) }}" 
                            data-email="{{ strtoupper($m->email ?? '') }}" 
                            data-status="{{ strtoupper($m->status) }}">
                            
                            <td class="px-2 py-1 text-[9.5px] font-mono font-bold text-center align-middle print-no-cell">{{ str_pad($subIndex + 1, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-2.5 py-1 text-[10px] font-black uppercase align-middle">{{ $m->nama_mentor }}</td>
                            <td class="px-2.5 py-1 text-[9.5px] font-medium text-slate-800 align-middle">{{ $m->email ?: '-' }}</td>
                            <td class="px-2.5 py-1 text-[9.5px] font-mono font-bold text-center align-middle">{{ $m->kontak ?: '-' }}</td>
                            <td class="px-2.5 py-1 text-[9px] font-black text-center uppercase align-middle">{{ $m->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> 
    @endforeach
</div>