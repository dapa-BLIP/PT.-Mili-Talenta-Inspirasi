// Membungkus kode dalam IIFE agar variabel internal tidak bocor atau ketiban script lain
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const filterSektor = document.getElementById('filter-sektor');
        const filterPelatihan = document.getElementById('filter-pelatihan');
        const filterDosen = document.getElementById('filter-dosen');
        const displayMentor = document.getElementById('display-mentor');
        const tableContainer = document.getElementById('matrix-table-container');
        const rowPeserta = document.querySelectorAll('.row-peserta');
        const matrixForm = document.getElementById('matrix-form');
        const btnSave = document.getElementById('btn-save-matrix');
        const notifContainer = document.getElementById('ajax-notification');
        const notifBox = document.getElementById('ajax-notif-box');
        const notifMsg = document.getElementById('ajax-notif-msg');

        // Jika element form tidak ditemukan di halaman ini, hentikan script agar tidak memicu error
        if (!matrixForm) return;

        let activeKomponenNilai = [];

        function bukaWarningModal(pesan) {
            document.getElementById('modal-warning-message').innerText = pesan;
            document.getElementById('matrix-warning-modal').classList.remove('hidden');
        }

        function lockAllScoreInputs() {
            tableContainer.classList.remove('opacity-100');
            tableContainer.classList.add('opacity-40', 'pointer-events-none');
        }

        function showNotif(msg, type) {
            notifMsg.textContent = msg;
            notifContainer.classList.remove('hidden');
            if (type === "success") {
                notifBox.className = "p-3 text-[10px] font-black uppercase tracking-widest flex justify-between items-center shadow-sm bg-emerald-50 border border-emerald-200 text-emerald-800";
            } else {
                notifBox.className = "p-3 text-[10px] font-black uppercase tracking-widest flex justify-between items-center shadow-sm bg-red-50 border border-red-200 text-red-800";
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Ekspos fungsi ke scope window agar onclick="" di HTML Blade tetap bisa memanggil fungsinya
        window.tutupWarningModal = function() {
            document.getElementById('matrix-warning-modal').classList.add('hidden');
        }

        window.jalankanExport = function() {
            const sektor = filterSektor.value;
            const pelatihanId = filterPelatihan.value;
            if (!sektor || !pelatihanId) {
                bukaWarningModal("Kunci Kategori Spektrum & Kelas Pelatihan terlebih dahulu sebelum mengunduh template!");
                return;
            }
            window.location.href = `/manajemen-peserta/nilai/export?spektrum_kategori=${encodeURIComponent(sektor)}&pelatihan_id=${encodeURIComponent(pelatihanId)}`;
        }

        window.jalankanImport = function() {
            const sektor = filterSektor.value;
            const pelatihanId = filterPelatihan.value;
            const dosenId = filterDosen.value;

            if (!sektor || !pelatihanId || !dosenId) {
                bukaWarningModal("Kunci filter Spektrum, Kelas Pelatihan, dan Mentor terlebih dahulu agar data Excel sinkron!");
                return;
            }

            const formImport = document.getElementById('form-import');
            formImport.querySelectorAll('.hidden-param').forEach(el => el.remove());
            formImport.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="pelatihan_id" value="${pelatihanId}" class="hidden-param">
                <input type="hidden" name="trainer_id" value="${dosenId}" class="hidden-param">
            `);
            document.getElementById('file-import').click();
        }

        // FILTER STEP 1: SPECTRUM -> AJAX LOAD KELAS PELATIHAN
        filterSektor.addEventListener('change', function() {
            const sektorVal = this.value;
            
            filterPelatihan.disabled = true;
            filterPelatihan.innerHTML = '<option value="">-- MEMUAT PROGRAM PELATIHAN... --</option>';
            filterPelatihan.className = "w-full px-3 py-2.5 bg-slate-50 border border-slate-200 text-[10px] font-black uppercase tracking-widest rounded-none cursor-not-allowed";
            
            displayMentor.value = "";
            filterDosen.value = "";
            lockAllScoreInputs();

            if(sektorVal) {
                let counter = 1;
                rowPeserta.forEach(row => {
                    const sektorBaris = row.getAttribute('data-sektor').trim().toLowerCase();
                    const sektorDipilih = sektorVal.trim().toLowerCase();

                    if(sektorBaris === sektorDipilih) {
                        row.style.display = "";
                        row.querySelector('.input-peserta-id').disabled = false;
                        row.querySelector('.index-kolom').textContent = String(counter).padStart(3, '0');
                        counter++;
                    } else {
                        row.style.display = "none";
                        row.querySelector('.input-peserta-id').disabled = true;
                    }
                });

                fetch(`/ajax/get-materi?sektor=${encodeURIComponent(sektorVal)}`)
                    .then(response => response.json())
                    .then(data => {
                        filterPelatihan.innerHTML = '<option value="">-- PILIH PROGRAM PELATIHAN --</option>';
                        filterPelatihan.disabled = false;
                        filterPelatihan.className = "w-full px-3 py-2.5 bg-white border border-slate-300 text-[10px] font-black uppercase tracking-widest rounded-none cursor-pointer";

                        data.forEach(p => {
                            const option = document.createElement('option');
                            option.value = p.id;
                            option.setAttribute('data-komponen', JSON.stringify(p.komponen_nilai));
                            option.setAttribute('data-mentor-id', p.trainer_id);
                            option.setAttribute('data-mentor-nama', p.mentor ? p.mentor.nama_mentor : 'BELUM DITUNJUK');
                            option.textContent = String(p.nama_pelatihan).toUpperCase();
                            filterPelatihan.appendChild(option);
                        });
                    });
            } else {
                lockAllScoreInputs();
            }
        });

        // FILTER STEP 2: KELAS SELECTED -> GENERATE MATRIKS
        filterPelatihan.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const pelatihanIdVal = this.value;

            if(!pelatihanIdVal) {
                displayMentor.value = "";
                filterDosen.value = "";
                lockAllScoreInputs();
                return;
            }

            filterDosen.value = selectedOption.getAttribute('data-mentor-id');
            displayMentor.value = selectedOption.getAttribute('data-mentor-nama').toUpperCase();

            const komponenRaw = selectedOption.getAttribute('data-komponen');
            try {
                activeKomponenNilai = JSON.parse(komponenRaw) || [];
            } catch(e) {
                activeKomponenNilai = ['NILAI_MURNI'];
            }

            // 🛠️ RE-BUILD HEADER MATRIKS DENGAN UKURAN TETAP AGAR TIDAK GEPENG
            const headerRow = document.getElementById('matrix-header-row');
            headerRow.innerHTML = `
                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase w-12 text-center sticky left-0 bg-white z-20 border-r border-slate-100">No.</th>
                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase w-72 sticky left-12 bg-white z-20 border-r border-slate-100 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.05)]">Participant Name</th>
            `;
            
            activeKomponenNilai.forEach(komponen => {
                const th = document.createElement('th');
                th.className = "px-3 py-4 text-[10px] font-black text-slate-600 uppercase text-center bg-slate-50 border-l border-slate-200 w-[120px] shrink-0";
                th.textContent = komponen.replace('_', ' ');
                headerRow.appendChild(th);
            });

            headerRow.insertAdjacentHTML('beforeend', `<th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase w-[300px]">Catatan / Evaluasi</th>`);

            tableContainer.classList.remove('opacity-40', 'pointer-events-none');
            tableContainer.classList.add('opacity-100');

            fetch(`/ajax/get-nilai-existing?pelatihan_id=${pelatihanIdVal}`)
                .then(response => response.json())
                .then(nilaiData => {
                    rowPeserta.forEach((row, rowIndex) => {
                        if (row.style.display === "none") return;

                        const pId = row.querySelector('.input-peserta-id').value;
                        const recordSiswa = nilaiData.find(item => item.peserta_id == pId);
                        
                        let subScores = {};
                        let catatanExisting = "";
                        if(recordSiswa) {
                            try {
                                subScores = typeof recordSiswa.scores === 'string' ? JSON.parse(recordSiswa.scores) : recordSiswa.scores;
                                catatanExisting = recordSiswa.catatan || "";
                            } catch(e) { subScores = {}; }
                        }

                        while (row.cells.length > 2) {
                            row.deleteCell(2);
                        }

                        // 🛠️ SUNTIK INPUT BOX NUMBER DENGAN WIDTH TETAP (w-[120px])
                        activeKomponenNilai.forEach(komponen => {
                            const keyLower = komponen.toLowerCase();
                            const valExisting = subScores[keyLower] !== undefined ? subScores[keyLower] : "";
                            
                            const td = row.insertCell(-1);
                            td.className = "px-3 py-3 bg-slate-50/50 border-l border-slate-200 w-[120px] shrink-0";
                            td.innerHTML = `
                                <input type="number" name="nilai[${rowIndex}][scores][${keyLower}]" min="0" max="100" placeholder="-" value="${valExisting}"
                                       class="input-pintar w-full text-center px-2 py-2 bg-white border border-slate-200 text-[11px] font-black text-slate-700 rounded-none focus:ring-1 focus:ring-[#22a0f6] focus:border-[#22a0f6]">
                            `;
                        });

                        // 🛠️ SUNTIK TEXTAREA/INPUT CATATAN DI PALING UJUNG KANAN (w-[300px])
                        const tdCatatan = row.insertCell(-1);
                        tdCatatan.className = "px-4 py-3 w-[300px] shrink-0";
                        tdCatatan.innerHTML = `
                            <input type="text" name="nilai[${rowIndex}][catatan]" placeholder="Ketik catatan..." value="${catatanExisting}"
                                   class="input-pintar w-full px-3 py-2 bg-white border border-slate-200 text-[10px] font-bold uppercase tracking-wide rounded-none placeholder-slate-300 focus:ring-1 focus:ring-[#22a0f6] focus:border-[#22a0f6]">
                        `;
                    });
                });
        });

        // INTERCEPT SUBMIT MATRIKS VIA AJAX
        matrixForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const originalText = btnSave.innerHTML;
            btnSave.innerHTML = "⏳ SAVING MATRIX DATA...";
            btnSave.disabled = true;

            const formData = new FormData(matrixForm);
            formData.append('pelatihan_id', filterPelatihan.value);
            formData.append('trainer_id', filterDosen.value);

            fetch(matrixForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                btnSave.innerHTML = originalText;
                btnSave.disabled = false;
                if (data.success) {
                    showNotif("✅ SUCCESS: " + data.message, "success");
                } else {
                    showNotif("❌ FAILED: " + data.message, "error");
                }
            })
            .catch(error => {
                btnSave.innerHTML = originalText;
                btnSave.disabled = false;
                showNotif("❌ ERROR: Gagal memproses data ke database server.", "error");
            });
        });
    });
})();