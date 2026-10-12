<script>
window.exportToExcel = function() {
    console.log('Engine Excel Dashboard Menyamping Aktif dengan Solid Border & Landscape View...');
    
    const selectBulan = document.getElementsByName('bulan')[0];
    const nameBulan = selectBulan ? selectBulan.options[selectBulan.selectedIndex].text.trim().toUpperCase() : '';
    const nameTahun = document.getElementsByName('tahun')[0] ? document.getElementsByName('tahun')[0].value : '';
    
    const currentObj = new Date();
    const dateString = currentObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

    const container = document.getElementById('report-content');
    if (!container) {
        console.error('Container report-content tidak ditemukan!');
        return;
    }

    const totalRegistrasi = container.getAttribute('data-total-registrasi') || 0;
    const programTerpopuler = container.getAttribute('data-program-terpopuler') || '-';
    const jumlahJenisProgram = container.getAttribute('data-jumlah-program') || 0;
    const priaTotal = container.getAttribute('data-pria-total') || 0;
    const priaPercent = container.getAttribute('data-pria-percent') || 0;
    const wanitaTotal = container.getAttribute('data-wanita-total') || 0;
    const wanitaPercent = container.getAttribute('data-wanita-percent') || 0;

    const mon = container.getAttribute('data-mon') || 0;
    const tue = container.getAttribute('data-tue') || 0;
    const wed = container.getAttribute('data-wed') || 0;
    const thu = container.getAttribute('data-thu') || 0;
    const fri = container.getAttribute('data-fri') || 0;
    const sat = container.getAttribute('data-sat') || 0;
    const sun = container.getAttribute('data-sun') || 0;

    let komposisiProgram = [];
    try {
        komposisiProgram = JSON.parse(container.getAttribute('data-komposisi-program') || '[]');
    } catch (e) {
        console.error('Gagal parse data-komposisi-program:', e);
    }

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Overview Realtime');

    // ==========================================
    // 🌟 KUNCI SETTINGAN LANDSCAPE & PRINT AREA
    // ==========================================
    worksheet.pageSetup = {
        orientation: 'landscape',  // Kunci layout mendatar (Landscape)
        paperSize: 9,              // Set ukuran kertas standar A4
        fitToPage: true,           // Aktifkan auto-fit skala saat di-print
        fitToWidth: 1,             // Kolom dipaksa muat dalam 1 lebar halaman kertas
        fitToHeight: 0             // Tinggi halaman dibiarkan dinamis mengikuti panjang data
    };

    // --- 1. HEADER DOKUMEN ---
    worksheet.getCell('A1').value = "PT MILI TALENTA INSPIRASI";
    worksheet.getCell('A1').font = { name: 'Arial', size: 16, bold: true, color: { argb: 'FF1E293B' } };
    
    worksheet.getCell('A2').value = "EXECUTIVE MONTHLY REPORT - PORTAL SYSTEM";
    worksheet.getCell('A2').font = { name: 'Arial', size: 11, bold: true, color: { argb: 'FF475569' } };

    worksheet.getCell('A4').value = "Periode Laporan:";
    worksheet.getCell('B4').value = `${nameBulan} ${nameTahun}`;
    worksheet.getCell('A5').value = "Tanggal Unduh:";
    worksheet.getCell('B5').value = dateString;
    worksheet.getCell('A4').font = worksheet.getCell('A5').font = { name: 'Arial', bold: true, size: 10 };

    // --- 2. PEMETAAN LAYOUT MENYAMPING (GRID SYSTEM) ---
    
    // ==========================================
    // SISI KIRI (KOLOM A s/d C): METRIK UTAMA & GENDER
    // ==========================================
    worksheet.getCell('A7').value = "1. METRIK UTAMA OPERASIONAL (REALTIME)";
    worksheet.getCell('A7').font = { name: 'Arial', size: 11, bold: true };
    
    worksheet.getRow(8).values = ["Indikator Analitik", "Nilai Realtime", "Satuan"];
    worksheet.getRow(9).values = ["Total Registrasi Baru", parseInt(totalRegistrasi), "Orang"];
    worksheet.getRow(10).values = ["Program Terpopuler", programTerpopuler.toUpperCase(), "-"];
    worksheet.getRow(11).values = ["Varian Program Dibuka", parseInt(jumlahJenisProgram), "Klaster"];
    worksheet.getRow(12).values = ["Rata-rata Harian Pendaftar", parseFloat((totalRegistrasi / 7).toFixed(1)), "Orang/Hari"];

    worksheet.getCell('A14').value = "2. KOMPOSISI GENDER PESERTA";
    worksheet.getCell('A14').font = { name: 'Arial', size: 11, bold: true };
    
    worksheet.getRow(15).values = ["KOMPOSISI GENDER", "TOTAL DATA", "PERSENTASE"];
    
    worksheet.getCell('A16').value = "Pria";
    worksheet.getCell('B16').value = parseInt(priaTotal) + " Orang";
    worksheet.getCell('C16').value = `${priaPercent}%`;

    worksheet.getCell('A17').value = "Wanita";
    worksheet.getCell('B17').value = parseInt(wanitaTotal) + " Orang";
    worksheet.getCell('C17').value = `${wanitaPercent}%`;

    // ==========================================
    // SISI TENGAH (KOLOM E s/d F): KLASTER PROGRAM AKTIF
    // ==========================================
    worksheet.getCell('E7').value = "3. KLASTER PROGRAM PELATIHAN AKTIF";
    worksheet.getCell('E7').font = { name: 'Arial', size: 11, bold: true };
    
    worksheet.getCell('E8').value = "Nama Klaster Pelatihan";
    worksheet.getCell('F8').value = "Jumlah Pendaftar";
    
    let currentMentorRow = 9;
    komposisiProgram.forEach(p => {
        worksheet.getCell(`E${currentMentorRow}`).value = p.pelatihan.toUpperCase();
        worksheet.getCell(`F${currentMentorRow}`).value = parseInt(p.total) + " Orang";
        currentMentorRow++;
    });

    // ==========================================
    // SISI KANAN (KOLOM H s/d I): TREN MINGGUAN
    // ==========================================
    worksheet.getCell('H7').value = "4. TREN PENDAFTARAN MINGGUAN (HARI)";
    worksheet.getCell('H7').font = { name: 'Arial', size: 11, bold: true };
    
    worksheet.getCell('H8').value = "Hari";
    worksheet.getCell('I8').value = "Jumlah Orang Terdaftar";

    const hariData = [
        ["Senin (Monday)", parseInt(mon) + " Orang"],
        ["Selasa (Tuesday)", parseInt(tue) + " Orang"],
        ["Rabu (Wednesday)", parseInt(wed) + " Orang"],
        ["Kamis (Thursday)", parseInt(thu) + " Orang"],
        ["Jumat (Friday)", parseInt(fri) + " Orang"],
        ["Sabtu (Saturday)", parseInt(sat) + " Orang"],
        ["Minggu (Sunday)", parseInt(sun) + " Orang"]
    ];

    hariData.forEach((hari, idx) => {
        let rNum = 9 + idx;
        worksheet.getCell(`H${rNum}`).value = hari[0];
        worksheet.getCell(`I${rNum}`).value = hari[1];
    });

    // --- 3. PEWARNAAN HEADER DENGAN THEME MAJU BLUE PT MILI ---
    const targetHeaders = [
        ['A8', 'B8', 'C8'], 
        ['A15', 'B15', 'C15'], 
        ['E8', 'F8'], 
        ['H8', 'I8']  
    ];

    targetHeaders.forEach(rowCells => {
        rowCells.forEach(pos => {
            const cell = worksheet.getCell(pos);
            cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: 'FF22A0F6' }
            };
            cell.font = { name: 'Arial', size: 10, bold: true, color: { argb: 'FFFFFFFF' } };
            cell.alignment = { vertical: 'middle', horizontal: 'center' };
        });
    });

    // --- 4. PASANG BORDER TABEL SOLID DAN TEGAS (FIXED GRID) ---
    const tableBorderStyle = {
        top: { style: 'thin', color: { argb: 'FF94A3B8' } },
        left: { style: 'thin', color: { argb: 'FF94A3B8' } },
        bottom: { style: 'thin', color: { argb: 'FF94A3B8' } },
        right: { style: 'thin', color: { argb: 'FF94A3B8' } }
    };

    const maxRowData = Math.max(17, currentMentorRow - 1, 15);

    for (let r = 8; r <= maxRowData; r++) {
        for (let c = 1; c <= 9; c++) {
            if (c === 4 || c === 7) continue;

            if (c >= 1 && c <= 3 && r <= 17) { 
                if (r === 13) continue; 
                
                const cell = worksheet.getCell(r, c);
                cell.border = tableBorderStyle;
                if (c > 1) cell.alignment = { horizontal: 'center', vertical: 'middle' };
            } 
            else if (c >= 5 && c <= 6 && r <= (currentMentorRow - 1)) { 
                const cell = worksheet.getCell(r, c);
                cell.border = tableBorderStyle;
                if (c === 6) cell.alignment = { horizontal: 'center', vertical: 'middle' };
            } 
            else if (c >= 8 && c <= 9 && r <= 15) { 
                const cell = worksheet.getCell(r, c);
                cell.border = tableBorderStyle;
                if (c === 9) cell.alignment = { horizontal: 'center', vertical: 'middle' };
            }
        }
    }

    // --- 5. SPACING GRID WIDTH SETTINGS ---
    worksheet.columns.forEach(column => {
        if (column.number === 4 || column.number === 7) {
            column.width = 4; 
        } else {
            column.width = 25; 
        }
    });

    // --- 6. TRIGGER DOWNLOAD GENERATOR ---
    workbook.xlsx.writeBuffer().then(buffer => {
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `PT_MILI_Executive_Report_${nameBulan}_${nameTahun}.xlsx`;
        link.click();
    });
};
</script>

