<!-- JAVASCRIPT MATRIX CONTROLLER (CASCADING 3-STEP FILTER & MATRIX ENGINE) -->
@php
    if (empty($programs)) {
        $kategoriPrograms = \App\Models\KategoriPelatihan::orderBy('nama_kategori', 'asc')->pluck('nama_kategori')->toArray();
        $pelatihanPrograms = \App\Models\Pelatihan::whereNotNull('jenis_pelatihan')->where('jenis_pelatihan', '!=', '')->distinct()->pluck('jenis_pelatihan')->toArray();
        $programs = array_values(array_unique(array_filter(array_merge($kategoriPrograms, $pelatihanPrograms))));
        sort($programs);
    }
    if (empty($mentors)) {
        $mentors = \App\Models\Trainer::where('status', 'AKTIF')->orderBy('nama_mentor', 'asc')->get();
    }
    if (empty($pelatihans)) {
        $pelatihans = \App\Models\Pelatihan::with('mentor')->orderBy('nama_pelatihan', 'asc')->get();
    }
@endphp
<script>
    const allPrograms = @json($programs);
    const allMentors = @json($mentors);
    const allPelatihans = @json($pelatihans);
    let currentKomponen = [];

    // Helper Normalisasi & Pencocokan Program Pelatihan
    function isSameProgram(progA, progB) {
        if (!progA || !progB) return false;
        const cleanA = String(progA).trim().toUpperCase();
        const cleanB = String(progB).trim().toUpperCase();
        if (cleanA === cleanB) return true;

        // Normalisasi kata awalan 'PELATIHAN ' atau 'PENGETAHUAN '
        const coreA = cleanA.replace(/^(PELATIHAN|PENGETAHUAN)\s+/, '').trim();
        const coreB = cleanB.replace(/^(PELATIHAN|PENGETAHUAN)\s+/, '').trim();
        if (coreA && coreB && coreA === coreB) {
            return true;
        }

        return false;
    }

    // Helper untuk mengecek apakah kelas pelatihan termasuk dalam program yang dipilih
    function isClassInProgram(pelatihan, selectedProg) {
        if (!pelatihan || !selectedProg) return false;
        if (isSameProgram(pelatihan.jenis_pelatihan, selectedProg)) return true;
        if (isSameProgram(pelatihan.nama_pelatihan, selectedProg)) return true;
        return false;
    }

    // Helper Styling Dropdown Aktif / Non-Aktif
    function setSelectState(selectElem, isEnabled, placeholderText) {
        if (!selectElem) return;
        selectElem.disabled = !isEnabled;
        if (isEnabled) {
            selectElem.classList.remove('bg-slate-50', 'text-slate-400', 'cursor-not-allowed');
            selectElem.classList.add('bg-white', 'text-slate-800', 'cursor-pointer');
        } else {
            selectElem.classList.remove('bg-white', 'text-slate-800', 'cursor-pointer');
            selectElem.classList.add('bg-slate-50', 'text-slate-400', 'cursor-not-allowed');
            selectElem.innerHTML = `<option value="">- ${placeholderText} -</option>`;
        }
    }

    // =========================================================================
    // 1. TAHAP 1: EVENT PROGRAM PELATIHAN DIUBAH
    // =========================================================================
    function handleProgramChange() {
        const selectProg = document.getElementById('filter-program');
        const selectMentor = document.getElementById('filter-mentor');
        const selectKelas = document.getElementById('filter-pelatihan');
        const statusInfo = document.getElementById('filter-status-info');
        const selectedProg = (selectProg ? selectProg.value : '').trim();

        // Selalu reset matrix table jika filter program berubah
        resetMatrixTable();

        if (!selectedProg) {
            setSelectState(selectMentor, false, 'PILIH PROGRAM DULU');
            setSelectState(selectKelas, false, 'PILIH MENTOR DULU');
            statusInfo.innerText = "● 1. Silakan Pilih Program Pelatihan";
            statusInfo.className = "text-[9px] font-bold text-amber-600 uppercase tracking-wide";
            return;
        }

        // 1. Cari kelas-kelas yang ada di program ini
        const classesInProg = allPelatihans.filter(p => isClassInProgram(p, selectedProg));
        const trainerIdsWithClasses = [...new Set(classesInProg.map(p => Number(p.trainer_id)))];

        // 2. Filter mentor yang HANYA mengampu kelas di program ini (atau master trainer di program ini)
        const matchedMentors = allMentors.filter(m => {
            const hasClassHere = trainerIdsWithClasses.includes(Number(m.id));
            const isMentorInProg = isSameProgram(m.program_pelatihan, selectedProg);
            return hasClassHere || (isMentorInProg && classesInProg.some(p => Number(p.trainer_id) === Number(m.id)));
        });

        // 3. JIKA TIDAK ADA MENTOR UNTUK PROGRAM INI:
        // JANGAN SEKALI-KALI MENAMPILKAN SEMUA MENTOR DARI PROGRAM LAIN!
        if (matchedMentors.length === 0) {
            setSelectState(selectMentor, false, 'BELUM ADA MENTOR DI PROGRAM INI');
            setSelectState(selectKelas, false, 'PILIH MENTOR DULU');
            statusInfo.innerText = "! Belum ada mentor atau kelas yang terdaftar untuk program ini di Kelola Pelatihan";
            statusInfo.className = "text-[9px] font-bold text-rose-600 uppercase tracking-wide";
            return;
        }

        // Aktifkan dropdown mentor dan isi dengan mentor yang SESUAI
        setSelectState(selectMentor, true, '');
        selectMentor.innerHTML = '<option value="">- PILIH MENTOR PENGAMPU -</option>';
        matchedMentors.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = String(m.nama_mentor).toUpperCase();
            selectMentor.appendChild(opt);
        });

        // Reset dropdown kelas
        setSelectState(selectKelas, false, 'PILIH MENTOR DULU');

        statusInfo.innerText = "● 2. Silakan Pilih Mentor Program Pelatihan";
        statusInfo.className = "text-[9px] font-bold text-amber-600 uppercase tracking-wide";
    }

    // =========================================================================
    // 2. TAHAP 2: EVENT MENTOR DIUBAH
    // =========================================================================
    function handleMentorChange() {
        const selectProg = document.getElementById('filter-program');
        const selectMentor = document.getElementById('filter-mentor');
        const selectKelas = document.getElementById('filter-pelatihan');
        const hiddenMentor = document.getElementById('filter-dosen');
        const statusInfo = document.getElementById('filter-status-info');

        const selectedMentorId = selectMentor ? selectMentor.value : '';
        const selectedProg = (selectProg ? selectProg.value : '').trim();

        // Reset matrix table
        resetMatrixTable();

        if (hiddenMentor) {
            hiddenMentor.value = selectedMentorId;
        }

        if (!selectedMentorId) {
            setSelectState(selectKelas, false, 'PILIH MENTOR DULU');
            statusInfo.innerText = "● 2. Silakan Pilih Mentor Program Pelatihan";
            statusInfo.className = "text-[9px] font-bold text-amber-600 uppercase tracking-wide";
            return;
        }

        // Filter kelas yang DIAMPU OLEH MENTOR INI DI DALAM PROGRAM INI:
        // HANYA ambil kelas yang cocok dengan mentor DAN cocok dengan program!
        const classesForMentor = allPelatihans.filter(p => {
            return Number(p.trainer_id) === Number(selectedMentorId) && isClassInProgram(p, selectedProg);
        });

        // JIKA TIDAK ADA KELAS UNTUK MENTOR INI DI PROGRAM INI:
        // JANGAN SEKALI-KALI MENAMPILKAN KELAS DARI PROGRAM LAIN!
        if (classesForMentor.length === 0) {
            selectKelas.innerHTML = '<option value="">- TIDAK ADA KELAS UNTUK MENTOR DI PROGRAM INI -</option>';
            setSelectState(selectKelas, false, 'TIDAK ADA KELAS DI PROGRAM INI');
            statusInfo.innerText = "! Mentor ini belum memiliki kelas terdaftar untuk program ini di Kelola Pelatihan";
            statusInfo.className = "text-[9px] font-bold text-rose-600 uppercase tracking-wide";
            return;
        }

        setSelectState(selectKelas, true, '');
        selectKelas.innerHTML = '<option value="">- PILIH KELAS PELATIHAN -</option>';
        classesForMentor.forEach(p => {
            let komponen = p.komponen_nilai || [];
            if (typeof komponen === 'string') {
                try { komponen = JSON.parse(komponen); } catch (e) { komponen = komponen.split(','); }
            }
            const cleanKomponen = (Array.isArray(komponen) ? komponen : []).map(k => String(k).trim()).filter(Boolean);
            const mentorName = p.mentor ? p.mentor.nama_mentor : (selectMentor.options[selectMentor.selectedIndex]?.text || 'MENTOR');

            const opt = document.createElement('option');
            opt.value = p.id;
            opt.setAttribute('data-trainer-id', p.trainer_id);
            opt.setAttribute('data-mentor', mentorName);
            opt.setAttribute('data-nama', p.nama_pelatihan);
            opt.setAttribute('data-jenis', p.jenis_pelatihan || '');
            opt.setAttribute('data-komponen', JSON.stringify(cleanKomponen));
            opt.textContent = `${p.nama_pelatihan} (${p.jenis_pelatihan || 'UMUM'})`;
            selectKelas.appendChild(opt);
        });

        statusInfo.innerText = "● 3. Silakan Pilih Kelas Pelatihan";
        statusInfo.className = "text-[9px] font-bold text-amber-600 uppercase tracking-wide";
    }

    // =========================================================================
    // 3. TAHAP 3: EVENT KELAS PELATIHAN DIUBAH (UNLOCK & POPULATE MATRIKS)
    // =========================================================================
    async function handlePelatihanChange() {
        const select = document.getElementById('filter-pelatihan');
        const pelatihanId = select ? select.value : '';
        const statusInfo = document.getElementById('filter-status-info');
        const tableContainer = document.getElementById('matrix-table-container');
        const badgesContainer = document.getElementById('komponen-badges-container');
        const infoLabel = document.getElementById('filter-kelas-info');

        resetMatrixInputs();

        if (!pelatihanId) {
            resetMatrixTable();
            statusInfo.innerText = "● 3. Silakan Pilih Kelas Pelatihan";
            statusInfo.className = "text-[9px] font-bold text-amber-600 uppercase tracking-wide";
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const mentorName = selectedOption.getAttribute('data-mentor') || 'BELUM DITENTUKAN';
        const trainerId = selectedOption.getAttribute('data-trainer-id') || document.getElementById('filter-mentor')?.value || '';
        const pelatihanNama = selectedOption.getAttribute('data-nama') || '';
        const jenisPelatihan = selectedOption.getAttribute('data-jenis') || '';
        const komponenJson = selectedOption.getAttribute('data-komponen') || '[]';

        try {
            currentKomponen = JSON.parse(komponenJson);
        } catch (e) {
            currentKomponen = [];
        }

        // Set Form Hidden Inputs
        document.getElementById('form-pelatihan-id').value = pelatihanId;
        document.getElementById('form-trainer-id').value = trainerId;
        if (document.getElementById('filter-dosen')) {
            document.getElementById('filter-dosen').value = trainerId;
        }

        // Render Badges Komponen Nilai
        badgesContainer.innerHTML = '';
        if (currentKomponen.length > 0) {
            currentKomponen.forEach(k => {
                const badge = document.createElement('span');
                badge.style.borderRadius = "0.125rem";
                badge.className = "px-1.5 py-0.5 bg-blue-50 text-[#22a0f6] border border-blue-100 text-[8px] font-black uppercase tracking-wider";
                badge.textContent = String(k).replace(/_/g, ' ');
                badgesContainer.appendChild(badge);
            });
        } else {
            badgesContainer.innerHTML = '<span class="text-[8.5px] text-amber-600 font-bold uppercase">Standar (UTS / UAS)</span>';
        }

        // Tampilkan Info Kelas & Mentor Terpilih
        if (infoLabel) {
            infoLabel.innerText = `KELAS: ${pelatihanNama.toUpperCase()} | MENTOR: ${mentorName.toUpperCase()}`;
        }

        // Filter Baris Peserta sesuai nama Pelatihan
        filterTableRowsByPelatihan(pelatihanNama, jenisPelatihan);

        // Rebuild Kolom Nilai Dinamis
        rebuildMatrixColumns(currentKomponen);

        // Aktifkan Form Input Matriks
        tableContainer.classList.remove('opacity-40', 'pointer-events-none');
        document.querySelectorAll('.row-peserta').forEach(row => {
            const isAktif = row.getAttribute('data-status') === 'AKTIF';
            if (isAktif) {
                row.querySelectorAll('.input-pintar').forEach(el => el.disabled = false);
            } else {
                row.querySelectorAll('.input-pintar').forEach(el => el.disabled = true);
            }
        });

        statusInfo.innerText = "Berhasil (Peserta Aktif Siap Dinilai)";
        statusInfo.className = "text-[9px] font-bold text-emerald-600 uppercase tracking-wide";

        // Load Nilai Yang Sudah Pernah Tersimpan Sebelumnya
        await loadExistingNilai(pelatihanId);
    }

    // =========================================================================
    // 4. FILTER BARIS SISWA SESUAI PELATIHAN
    // =========================================================================
    function filterTableRowsByPelatihan(pelatihanNama, jenisPelatihan) {
        const rows = document.querySelectorAll('.row-peserta');
        let count = 0;
        const targetClean = (pelatihanNama || '').toLowerCase().replace(/kelas/g, '').replace(/[^a-z0-9]/g, ' ').trim();
        const jenisClean = (jenisPelatihan || '').toLowerCase().replace(/pelatihan|pengetahuan/g, '').replace(/[^a-z0-9]/g, ' ').trim();

        rows.forEach(row => {
            const raw = (row.getAttribute('data-pelatihan') || '').toLowerCase().replace(/kelas/g, '').replace(/[^a-z0-9]/g, ' ').trim();
            let isMatch = false;

            if (!pelatihanNama) {
                isMatch = true;
            } else {
                const rawHasNon = raw.includes('non');
                const targetHasNon = targetClean.includes('non');

                if (rawHasNon !== targetHasNon) {
                    isMatch = false;
                } else if (raw === targetClean) {
                    isMatch = true;
                } else if (raw.includes(targetClean) || targetClean.includes(raw)) {
                    isMatch = true;
                } else if (jenisClean && jenisClean.length > 2 && raw.includes(jenisClean)) {
                    isMatch = true;
                }
            }

            if (isMatch) {
                row.style.display = '';
                count++;
                const idxCol = row.querySelector('.index-kolom');
                if (idxCol) idxCol.textContent = String(count).padStart(4, '0');
            } else {
                row.style.display = 'none';
            }
        });

        const emptyRow = document.getElementById('row-empty-peserta');
        if (emptyRow) {
            emptyRow.classList.toggle('hidden', count > 0);
        }

        document.getElementById('visible-count').textContent = String(count).padStart(2, '0');
    }

    // =========================================================================
    // 5. REBUILD KOLOM DINAMIS (LEBAR & TINGGI COMPACT)
    // =========================================================================
    function rebuildMatrixColumns(komponenList) {
        document.querySelectorAll('.th-dynamic-score').forEach(el => el.remove());
        document.querySelectorAll('.td-dynamic-score').forEach(el => el.remove());

        const thCatatan = document.getElementById('th-catatan');

        // Header Kolom Komponen
        komponenList.forEach(k => {
            const th = document.createElement('th');
            th.className = "th-dynamic-score w-16 px-1.5 py-2 text-[9px] font-black uppercase tracking-wider text-center text-slate-500 bg-slate-50/80 border-r border-slate-100";
            th.innerText = String(k).replace(/_/g, ' ').toUpperCase();
            thCatatan.parentNode.insertBefore(th, thCatatan);
        });

        // Input Sel Nilai Setiap Baris Peserta
        document.querySelectorAll('.row-peserta').forEach((row, rowIndex) => {
            const cellCatatan = row.querySelector('.cell-catatan');
            const isAktif = row.getAttribute('data-status') === 'AKTIF';

            komponenList.forEach(k => {
                const cleanKey = String(k).toLowerCase().replace(/[^a-z0-9]/g, '_');
                const td = document.createElement('td');
                td.className = "td-dynamic-score w-16 px-1 py-1.5 text-center align-middle border-r border-slate-100";

                td.innerHTML = `
                    <input type="number" min="0" max="100" step="1" 
                        name="nilai[${rowIndex}][scores][${cleanKey}]" 
                        data-komponen="${cleanKey}"
                        placeholder="${isAktif ? '0' : '-'}" 
                        ${isAktif ? '' : 'disabled'}
                        style="border-radius: 0.125rem;"
                        class="input-pintar input-score-dynamic w-12 h-7 mx-auto px-1 bg-white border border-slate-300 text-[10px] font-black text-center text-slate-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs ${isAktif ? '' : 'bg-slate-100 text-slate-400 cursor-not-allowed'}">
                `;
                cellCatatan.parentNode.insertBefore(td, cellCatatan);
            });
        });
    }

    // =========================================================================
    // 6. LOAD NILAI EXISTING DARI SERVER
    // =========================================================================
    async function loadExistingNilai(pelatihanId) {
        try {
            const res = await fetch(`{{ route('nilai.getNilaiExisting') }}?pelatihan_id=${encodeURIComponent(pelatihanId)}`);
            const existingData = await res.json();

            document.querySelectorAll('.row-peserta').forEach(row => {
                const pesertaId = row.getAttribute('data-peserta-id');
                const catatanInput = row.querySelector('.input-keterangan');
                const isAktif = row.getAttribute('data-status') === 'AKTIF';

                if (catatanInput && isAktif) catatanInput.value = '';
                row.querySelectorAll('.input-score-dynamic').forEach(input => input.value = '');

                const match = existingData.find(d => d.peserta_id == pesertaId);
                if (match) {
                    if (match.scores) {
                        Object.keys(match.scores).forEach(k => {
                            const target = row.querySelector(`input[data-komponen="${k}"]`);
                            if (target) target.value = match.scores[k];
                        });
                    }
                    if (match.catatan && catatanInput && isAktif) {
                        catatanInput.value = match.catatan;
                    }
                }
            });
        } catch (err) {
            console.error("Gagal load nilai existing:", err);
        }
    }

    // =========================================================================
    // 7. SUBMIT MATRIX VIA AJAX
    // =========================================================================
    async function handleMatrixSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('matrix-form');
        const btnSave = document.getElementById('btn-save-matrix');
        const originalContent = btnSave.innerHTML;

        btnSave.disabled = true;
        btnSave.innerText = 'MENYIMPAN...';

        const formData = new FormData(form);

        try {
            const res = await fetch("{{ route('nilai.store') }}", {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await res.json();

            if (result.success) {
                const notif = document.getElementById('ajax-notification');
                document.getElementById('ajax-notif-msg').innerText = result.message;
                notif.classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                alert(result.message || 'Gagal menyimpan data.');
            }
        } catch (err) {
            alert('Terjadi kendala saat menyimpan.');
        } finally {
            btnSave.disabled = false;
            btnSave.innerHTML = originalContent;
        }
    }

    // =========================================================================
    // 8. RESET MATRIX
    // =========================================================================
    function resetMatrixTable() {
        const tableContainer = document.getElementById('matrix-table-container');
        if (tableContainer) {
            tableContainer.classList.add('opacity-40', 'pointer-events-none');
        }
        resetMatrixInputs();

        const formPelatihanId = document.getElementById('form-pelatihan-id');
        const formTrainerId = document.getElementById('form-trainer-id');
        const visibleCount = document.getElementById('visible-count');
        const badgesContainer = document.getElementById('komponen-badges-container');
        const infoLabel = document.getElementById('filter-kelas-info');

        if (formPelatihanId) formPelatihanId.value = '';
        if (formTrainerId) formTrainerId.value = '';
        if (visibleCount) visibleCount.textContent = '00';
        if (badgesContainer) {
            badgesContainer.innerHTML = '<span id="komponen-placeholder" class="text-[9px] text-slate-400 italic">Pilih kelas untuk memuat komponen</span>';
        }
        if (infoLabel) {
            infoLabel.innerText = '';
        }
    }

    function resetMatrixInputs() {
        document.querySelectorAll('.input-pintar').forEach(el => {
            el.disabled = true;
            el.value = '';
        });
    }

    // =========================================================================
    // 9. EXCEL EXPORT CONTROLLER (VALIDASI WAJIB 3 FILTER: PROGRAM, MENTOR, KELAS)
    // =========================================================================
    function handleExportNilai(e) {
        if (e && typeof e.preventDefault === 'function') {
            e.preventDefault();
        }

        const progSelect = document.getElementById('filter-program');
        const mentorSelect = document.getElementById('filter-mentor');
        const kelasSelect = document.getElementById('filter-pelatihan');

        const progVal = progSelect ? progSelect.value.trim() : '';
        const mentorVal = mentorSelect ? mentorSelect.value.trim() : '';
        const kelasVal = kelasSelect ? kelasSelect.value.trim() : '';

        // Validasi: Ketiga filter harus terisi lengkap
        if (!progVal || !mentorVal || !kelasVal) {
            let missingItems = [];
            if (!progVal) {
                missingItems.push("1. Program Pelatihan belum dipilih");
            }
            if (!mentorVal) {
                missingItems.push("2. Mentor Pengampu belum dipilih");
            }
            if (!kelasVal) {
                missingItems.push("3. Kelas Pelatihan belum dipilih");
            }

            const messageContainer = document.getElementById('modal-warning-message');
            if (messageContainer) {
                messageContainer.innerHTML = `
                    <div class="space-y-2.5">
                        <div class="flex items-start gap-2">
                            <span class="text-rose-500 font-black text-sm shrink-0">✕</span>
                            <p class="text-xs font-black text-slate-800 uppercase tracking-tight">
                                Persyaratan Export Excel Belum Terpenuhi
                            </p>
                        </div>
                        <p class="text-[11px] font-bold text-slate-600 leading-relaxed">
                            Untuk mengekspor rekap nilai yang sesuai, Anda wajib melengkapi ke-3 parameter filter berikut terlebih dahulu:
                        </p>
                        <div class="p-2.5 bg-rose-50 border border-rose-200 rounded-xs space-y-1">
                            ${missingItems.map(item => `
                                <div class="flex items-center gap-1.5 text-[10.5px] font-extrabold text-rose-700 uppercase tracking-wide">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                    <span>${item}</span>
                                </div>
                            `).join('')}
                        </div>
                        <p class="text-[9.5px] font-medium text-slate-400 italic">
                            * Silakan lengkapi pemilihan filter di atas agar berkas Excel yang diunduh sesuai dengan data kelas sasaran.
                        </p>
                    </div>
                `;
            }

            const modal = document.getElementById('matrix-warning-modal');
            if (modal) {
                modal.classList.remove('hidden');
            }
            return false;
        }

        // Ketiga filter valid: Redirect ke route export dengan parameter pelatihan_id
        const exportUrl = "{{ route('nilai.export') }}?pelatihan_id=" + encodeURIComponent(kelasVal);
        window.location.href = exportUrl;
        return true;
    }

    function tutupWarningModal() {
        const modal = document.getElementById('matrix-warning-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Listener Keyboard ESC dan Backdrop Click untuk Modal Peringatan
    document.addEventListener('keydown', function(evt) {
        if (evt.key === 'Escape') {
            tutupWarningModal();
        }
    });

    document.addEventListener('click', function(evt) {
        const modal = document.getElementById('matrix-warning-modal');
        if (modal && !modal.classList.contains('hidden') && evt.target === modal) {
            tutupWarningModal();
        }
    });
</script>
