<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>

    <div class="p-6 md:p-8 max-w-[1600px] mx-auto min-h-screen" 
         id="report-content"
         data-total-registrasi="{{ $totalRegistrasi }}"
         data-program-terpopuler="{{ $programTerpopuler }}"
         data-jumlah-program="{{ $jumlahJenisProgram }}"
         data-pria-total="{{ $priaTotal }}"
         data-pria-percent="{{ $priaPercent }}"
         data-wanita-total="{{ $wanitaTotal }}"
         data-wanita-percent="{{ $wanitaPercent }}"
         data-mon="{{ $daysOfWeek['Monday'] ?? 0 }}"
         data-tue="{{ $daysOfWeek['Tuesday'] ?? 0 }}"
         data-wed="{{ $daysOfWeek['Wednesday'] ?? 0 }}"
         data-thu="{{ $daysOfWeek['Thursday'] ?? 0 }}"
         data-fri="{{ $daysOfWeek['Friday'] ?? 0 }}"
         data-sat="{{ $daysOfWeek['Saturday'] ?? 0 }}"
         data-sun="{{ $daysOfWeek['Sunday'] ?? 0 }}"
         data-komposisi-program="{{ json_encode($komposisiProgram) }}">
        
        <!-- HEADER TOP, FILTER & PRINT KOP -->
        @include('dashboard.sub-data-ds.header-dashboard')

        <!-- SUMMARY CARDS (6 KOLOM) -->
        @include('dashboard.sub-data-ds.summary-cards')

        <!-- PROGRAM COMPOSITION & GENDER GRID -->
        @include('dashboard.sub-data-ds.chart-komposisi')

        <!-- ACTIVITY TREND & FEEDBACK ANALYTICS -->
        @include('dashboard.sub-data-ds.chart-aktivitas')
    </div>

    <!-- STYLES & SCRIPT INCLUDES -->
    @include('dashboard.sub-data-ds.style-dashboard')
    @include('dashboard.sub-data-ds.script-dashboard')
</x-app-layout>