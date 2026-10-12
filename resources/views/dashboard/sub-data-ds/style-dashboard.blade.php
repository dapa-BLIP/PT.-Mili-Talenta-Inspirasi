<!-- STYLES ENGINE CETAK -->
<style>
    select, select option {
        text-align-last: center !important;
        text-align: center !important;
    }

    @page {
        size: landscape;
        margin: 6mm;
    }

    @media print {
        aside, header, nav, .print\:hidden { 
            display: none !important; 
        }
        body, main, #report-content {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            background: white !important;
            color: #0f172a !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .shadow-xs, .shadow-sm, .shadow-2xl {
            box-shadow: none !important;
        }
        .page-break-avoid {
            page-break-inside: avoid;
        }
        #cell-rata-rata {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
        }
    }
</style>

