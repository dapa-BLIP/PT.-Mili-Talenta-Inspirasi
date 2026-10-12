<script>
(function () {
    // UTF-8 Safe Base64 Decode Helper
    function safeBase64Decode(str) {
        if (!str) return '';
        try {
            const binary = atob(str);
            const bytes = new Uint8Array(binary.length);
            for (let i = 0; i < binary.length; i++) {
                bytes[i] = binary.charCodeAt(i);
            }
            return new TextDecoder('utf-8').decode(bytes);
        } catch (e) {
            try {
                return decodeURIComponent(escape(atob(str)));
            } catch (err) {
                return str;
            }
        }
    }

    // Realtime System Clock Component (WIB)
    function updateClock() {
        const clockElement = document.getElementById('realtime-clock');
        if (!clockElement) return;

        const options = {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };

        const formatter = new Intl.DateTimeFormat('id-ID', options);
        let timeString = formatter.format(new Date());
        timeString = timeString.replace(/\./g, ':');
        clockElement.textContent = timeString;
    }

    // Inisialisasi Event Listener
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('realtime-clock')) {
            updateClock();
            setInterval(updateClock, 1000);
        }

        // Date Interlock Restriction Constraints (Filter)
        const filterStart = document.getElementById('filter_start_date');
        const filterEnd = document.getElementById('filter_end_date');

        if (filterStart && filterEnd) {
            filterStart.addEventListener('change', function() {
                if (this.value) {
                    filterEnd.min = this.value;
                    if (filterEnd.value && filterEnd.value < this.value) filterEnd.value = '';
                } else {
                    filterEnd.removeAttribute('min');
                }
            });

            filterEnd.addEventListener('change', function() {
                if (this.value) {
                    filterStart.max = this.value;
                    if (filterStart.value && filterStart.value > this.value) filterStart.value = '';
                } else {
                    filterStart.removeAttribute('max');
                }
            });

            if (filterStart.value) filterEnd.min = filterStart.value;
            if (filterEnd.value) filterStart.max = filterEnd.value;
        }

        // Date Interlock Restriction Constraints (Create Form)
        const createStart = document.getElementById('create_tgl_mulai');
        const createEnd = document.getElementById('create_tgl_selesai');
        if (createStart && createEnd) {
            createStart.addEventListener('change', function() {
                if (this.value) {
                    createEnd.min = this.value;
                    if (createEnd.value && createEnd.value < this.value) createEnd.value = this.value;
                }
            });
        }

        // Date Interlock Restriction Constraints (Edit Form)
        const editStart = document.getElementById('edit_tgl_mulai');
        const editEnd = document.getElementById('edit_tgl_selesai');
        if (editStart && editEnd) {
            editStart.addEventListener('change', function() {
                if (this.value) {
                    editEnd.min = this.value;
                    if (editEnd.value && editEnd.value < this.value) editEnd.value = this.value;
                }
            });
        }

        // Global Modal Escape & Backdrop Click
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeCreateModal();
                window.closeEditModal();
                window.closeDetailJadwal();
                window.closeDeleteModal();
            }
        });

        ['modalDetailJadwal', 'modalJadwal', 'modalEditJadwal', 'modalDeleteJadwal'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el && el.parentElement !== document.body) {
                document.body.appendChild(el);
            }
        });

        ['modalJadwal', 'modalEditJadwal', 'modalDetailJadwal', 'modalDeleteJadwal'].forEach(function(modalId) {
            const modalEl = document.getElementById(modalId);
            if (modalEl) {
                modalEl.addEventListener('click', function(e) {
                    if (e.target === modalEl) {
                        modalEl.classList.add('hidden');
                    }
                });
            }
        });
    });

    // =========================================================================
    // EXPOSE MODAL FUNCTIONS TO WINDOW SCOPE
    // =========================================================================

    // 1. CREATE MODAL
    window.openCreateModal = function() { 
        const m = document.getElementById('modalJadwal');
        if (m) {
            if (m.parentElement !== document.body) document.body.appendChild(m);
            m.classList.remove('hidden');
        }
    };
    window.closeCreateModal = function() { 
        const m = document.getElementById('modalJadwal');
        if (m) m.classList.add('hidden');
    };

    // 2. EDIT MODAL
    window.openEditModal = function(data, isBase64 = false) {
        const modal = document.getElementById('modalEditJadwal');
        const form = document.getElementById('formEditJadwal');
        
        if (!modal || !form) return;
        if (modal.parentElement !== document.body) document.body.appendChild(modal);
        
        form.action = `/jadwal/${data.id}`;
        
        let judulClean = isBase64 ? safeBase64Decode(data.judul) : data.judul;
        let descClean = isBase64 ? safeBase64Decode(data.description) : data.description;

        if (document.getElementById('edit_judul')) document.getElementById('edit_judul').value = judulClean;
        if (document.getElementById('edit_description')) document.getElementById('edit_description').value = descClean;
        if (document.getElementById('edit_trainer')) document.getElementById('edit_trainer').value = data.trainer_id;
        if (document.getElementById('edit_jenis')) document.getElementById('edit_jenis').value = (data.jenis || 'offline').toLowerCase();
        
        if (data.tgl_mulai && document.getElementById('edit_tgl_mulai')) {
            document.getElementById('edit_tgl_mulai').value = data.tgl_mulai;
            if (document.getElementById('edit_tgl_selesai')) {
                document.getElementById('edit_tgl_selesai').min = data.tgl_mulai;
            }
        }
        if (data.tgl_selesai && document.getElementById('edit_tgl_selesai')) {
            document.getElementById('edit_tgl_selesai').value = data.tgl_selesai;
        }
        
        if (data.jam_mulai && document.getElementById('edit_jam_mulai')) {
            document.getElementById('edit_jam_mulai').value = data.jam_mulai.substring(0, 5);
        }
        if (data.jam_selesai && document.getElementById('edit_jam_selesai')) {
            document.getElementById('edit_jam_selesai').value = data.jam_selesai.substring(0, 5);
        }

        modal.classList.remove('hidden');
    };

    window.closeEditModal = function() {
        const modal = document.getElementById('modalEditJadwal');
        if (modal) modal.classList.add('hidden');
    };

    // 3. DETAIL MODAL
    window.openDetailJadwalFromRow = function(rowElement) {
        try {
            const rawData = rowElement.getAttribute('data-jadwal');
            if (!rawData) return;
            let data;
            try {
                const decoded = safeBase64Decode(rawData);
                data = JSON.parse(decoded);
            } catch (err) {
                data = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;
            }
            window.openDetailJadwal(data, false);
        } catch(e) {
            console.error("Gagal parse data detail jadwal:", e);
        }
    };

    window.openDetailJadwal = function(data, isBase64 = false) {
        const modal = document.getElementById('modalDetailJadwal');
        if (!modal) return;
        if (modal.parentElement !== document.body) document.body.appendChild(modal);

        let judulClean = isBase64 ? safeBase64Decode(data.judul) : data.judul;
        let descClean = isBase64 ? safeBase64Decode(data.description) : data.description;
        let trainerName = isBase64 ? safeBase64Decode(data.trainer_name || '') : (data.trainer_name || 'Tidak Ada Mentor');

        // Set judul & trainer
        const elJudul = document.getElementById('detail_jadwal_judul');
        if (elJudul) elJudul.textContent = judulClean || 'Detail Jadwal Pelatihan';

        const elTrainer = document.getElementById('detail_jadwal_trainer');
        if (elTrainer) elTrainer.textContent = trainerName;

        // Set Periode Tanggal
        const elPeriode = document.getElementById('detail_jadwal_periode');
        if (elPeriode) {
            let mulai = data.tgl_mulai || '-';
            let selesai = data.tgl_selesai || '-';
            elPeriode.textContent = `${mulai}  sampai  ${selesai}`;
        }

        // Set Jam Pelaksanaan
        const elJam = document.getElementById('detail_jadwal_jam');
        if (elJam) {
            let jMulai = data.jam_mulai ? data.jam_mulai.substring(0, 5) : '00:00';
            let jSelesai = data.jam_selesai ? data.jam_selesai.substring(0, 5) : '00:00';
            elJam.textContent = `${jMulai} - ${jSelesai} WIB`;
        }

        // Set Deskripsi
        const elDesc = document.getElementById('detail_jadwal_deskripsi');
        if (elDesc) {
            elDesc.textContent = descClean && descClean.trim() !== '' ? descClean : 'Tidak ada catatan atau instruksi khusus.';
        }

        // Set Metode Pelaksanaan
        const isOffline = (data.jenis || 'offline').toLowerCase() === 'offline';
        const elJenisBadge = document.getElementById('detail_jadwal_jenis_badge');
        const elJenisDesc = document.getElementById('detail_jadwal_jenis_desc');
        if (elJenisBadge && elJenisDesc) {
            if (isOffline) {
                elJenisBadge.className = 'px-2 py-0.5 text-[8.5px] font-black uppercase rounded-xs tracking-wider border bg-amber-50 text-amber-700 border-amber-200';
                elJenisBadge.textContent = 'ON-SITE';
                elJenisDesc.textContent = 'Tatap Muka / Langsung';
            } else {
                elJenisBadge.className = 'px-2 py-0.5 text-[8.5px] font-black uppercase rounded-xs tracking-wider border bg-blue-50 text-blue-700 border-blue-200';
                elJenisBadge.textContent = 'ONLINE';
                elJenisDesc.textContent = 'Virtual / Remote Session';
            }
        }

        // Status badge
        const elStatus = document.getElementById('detail_jadwal_status_badge');
        if (elStatus) {
            let isComplete = data.is_complete === true || data.is_complete === 'true' || data.is_complete === 1 || data.is_complete === '1';
            if (isComplete) {
                elStatus.className = 'px-2.5 py-1 text-[8.5px] font-black uppercase tracking-wider rounded-sm border bg-emerald-50 text-emerald-700 border-emerald-300';
                elStatus.textContent = 'COMPLETE';
            } else {
                elStatus.className = 'px-2.5 py-1 text-[8.5px] font-black uppercase tracking-wider rounded-sm border bg-amber-50 text-amber-700 border-amber-300';
                elStatus.textContent = 'ON-GOING';
            }
        }

        // Feedback Box (khusus offline)
        const feedbackBox = document.getElementById('detail_jadwal_feedback_box');
        const btnCopyFeedback = document.getElementById('btnDetailCopyFeedback');
        if (feedbackBox) {
            if (isOffline) {
                feedbackBox.classList.remove('hidden');
                if (btnCopyFeedback) {
                    btnCopyFeedback.onclick = function() {
                        window.copyFeedbackLink(judulClean, data.google_form_url || 'https://docs.google.com/forms/');
                    };
                }
            } else {
                feedbackBox.classList.add('hidden');
            }
        }

        modal.classList.remove('hidden');
    };

    window.closeDetailJadwal = function() {
        const modal = document.getElementById('modalDetailJadwal');
        if (modal) modal.classList.add('hidden');
    };

    // 4. DELETE MODAL
    window.triggerDeleteModal = function(id, title) {
        const modal = document.getElementById('modalDeleteJadwal');
        const form = document.getElementById('globalDeleteForm');
        const titleTarget = document.getElementById('deleteTargetTitle');
        const btnConfirm = document.getElementById('btnConfirmDelete');
        
        if (!modal || !form || !titleTarget) return;
        
        titleTarget.innerText = title;
        form.action = `/jadwal/${id}`;
        
        btnConfirm.onclick = function() {
            form.submit();
        };
        
        modal.classList.remove('hidden');
    };

    window.closeDeleteModal = function() {
        const modal = document.getElementById('modalDeleteJadwal');
        if (modal) modal.classList.add('hidden');
    };

})();
</script>

