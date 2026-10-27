/**
 * PMM AutoCare — JavaScript Interaktivitas Dashboard Admin
 * - Logika Modal Popup Terima / Tolak Pesanan & Penugasan Mekanik
 * - Logika Modal Popup Manajemen Slot & Jadwal Servis (Modul Schedule)
 *
 * Notifikasi & konfirmasi memakai komponen global `PMMAlert`
 * (resources/views/popup/alert.blade.php). Bila partial itu belum
 * di-include di halaman, otomatis jatuh kembali ke dialog bawaan browser.
 */

document.addEventListener('DOMContentLoaded', () => {

    /**
     * Token CSRF dari <meta name="csrf-token"> di layout.
     *
     * Dipakai bersama oleh seluruh permintaan fetch (form pesanan, modal slot,
     * dan toggle slot). Tanpa token ini Laravel membalas 419.
     */
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    /**
     * Pembungkus aman ke PMMAlert dengan fallback dialog browser.
     */
    const notify = {
        success: (message, options) => callAlert('success', message, options),
        error: (message, options) => callAlert('error', message, options),
        warning: (message, options) => callAlert('warning', message, options),
        info: (message, options) => callAlert('info', message, options),
        confirm: (message, options) => {
            if (window.PMMAlert?.confirm) {
                return window.PMMAlert.confirm(message, options);
            }

            return Promise.resolve(window.confirm(message));
        },
    };

    function callAlert(variant, message, options) {
        if (window.PMMAlert?.[variant]) {
            window.PMMAlert[variant](message, options);

            return;
        }

        window.alert(message);
    }

    /**
     * Muat ulang halaman setelah popup sempat terbaca pengguna.
     *
     * @param {number} [delay] jeda sebelum reload (ms)
     */
    function reloadAfter(delay = 1100) {
        window.setTimeout(() => window.location.reload(), delay);
    }

    // ============================================================
    // 1. MODAL PROSES PESANAN (TERIMA / TOLAK)
    // ============================================================
    const modalPesanan = document.getElementById('modal-proses-pesanan');
    const modalCardPesanan = document.getElementById('modal-card');
    const btnCloseModalPesanan = document.getElementById('btn-close-modal');
    const btnsCancelModalPesanan = document.querySelectorAll('.btn-cancel-modal');

    const selectAksi = document.getElementById('select-aksi-pesanan');
    const formTerima = document.getElementById('form-terima-modal');
    const formTolak = document.getElementById('form-tolak-modal');

    const pesananKodeEl = document.getElementById('modal-pesanan-kode');
    const pesananPelangganEl = document.getElementById('modal-pesanan-pelanggan');
    const inputTerimaId = document.getElementById('input-terima-id-pesanan');
    const inputTolakId = document.getElementById('input-tolak-id-pesanan');

    function openModalPesanan(data) {
        if (!modalPesanan) return;

        const { id, kode, pelanggan, terimaUrl, tolakUrl } = data;

        if (pesananKodeEl) pesananKodeEl.textContent = kode || `PMM-${id}`;
        if (pesananPelangganEl) pesananPelangganEl.textContent = pelanggan || 'Pelanggan';
        if (inputTerimaId) inputTerimaId.value = id;
        if (inputTolakId) inputTolakId.value = id;

        // URL aksi berasal dari route() di sisi server (bukan disusun di JS),
        // sehingga tetap benar apa pun bentuk kunci primernya.
        if (formTerima && terimaUrl) formTerima.action = terimaUrl;
        if (formTolak && tolakUrl) formTolak.action = tolakUrl;

        if (selectAksi) {
            selectAksi.value = 'terima';
            switchFormAksi('terima');
        }

        modalPesanan.classList.remove('hidden');
        setTimeout(() => {
            modalPesanan.classList.remove('opacity-0');
            modalPesanan.classList.add('opacity-100');
            if (modalCardPesanan) {
                modalCardPesanan.classList.remove('scale-95');
                modalCardPesanan.classList.add('scale-100');
            }
        }, 10);
    }

    function closeModalPesanan() {
        if (!modalPesanan) return;

        modalPesanan.classList.remove('opacity-100');
        modalPesanan.classList.add('opacity-0');
        if (modalCardPesanan) {
            modalCardPesanan.classList.remove('scale-100');
            modalCardPesanan.classList.add('scale-95');
        }

        setTimeout(() => {
            modalPesanan.classList.add('hidden');
        }, 200);
    }

    function switchFormAksi(aksi) {
        if (aksi === 'terima') {
            formTerima?.classList.remove('hidden');
            formTolak?.classList.add('hidden');
        } else if (aksi === 'tolak') {
            formTolak?.classList.remove('hidden');
            formTerima?.classList.add('hidden');
        }
    }

    document.querySelectorAll('.btn-open-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            const data = {
                id: this.dataset.id,
                kode: this.dataset.kode,
                pelanggan: this.dataset.pelanggan,
                mobil: this.dataset.mobil,
                terimaUrl: this.dataset.terimaUrl,
                tolakUrl: this.dataset.tolakUrl
            };
            openModalPesanan(data);
        });
    });

    if (selectAksi) {
        selectAksi.addEventListener('change', function () {
            switchFormAksi(this.value);
        });
    }

    if (btnCloseModalPesanan) btnCloseModalPesanan.addEventListener('click', closeModalPesanan);
    btnsCancelModalPesanan.forEach(btn => btn.addEventListener('click', closeModalPesanan));

    if (modalPesanan) {
        modalPesanan.addEventListener('click', (e) => {
            if (e.target === modalPesanan) closeModalPesanan();
        });
    }

    // ============================================================
    // 2. MODAL MANAJEMEN SLOT (TAMBAH & EDIT SLOT)
    // ============================================================
    const modalTambahSlot = document.getElementById('modal-tambah-slot');
    const modalTambahCard = document.getElementById('modal-tambah-card');
    const btnOpenTambahSlot = document.getElementById('btn-open-tambah-slot');
    const btnsCloseTambahSlot = document.querySelectorAll('.btn-close-tambah-slot');
    const formTambahSlot = document.getElementById('form-tambah-slot');

    const modalEditSlot = document.getElementById('modal-edit-slot');
    const modalEditCard = document.getElementById('modal-edit-card');
    const btnsCloseEditSlot = document.querySelectorAll('.btn-close-edit-slot');
    const formEditSlot = document.getElementById('form-edit-slot');

    // Buka Modal Tambah Slot
    function openModalTambahSlot() {
        if (!modalTambahSlot) return;
        modalTambahSlot.classList.remove('hidden');
        setTimeout(() => {
            modalTambahSlot.classList.remove('opacity-0');
            modalTambahSlot.classList.add('opacity-100');
            if (modalTambahCard) {
                modalTambahCard.classList.remove('scale-95');
                modalTambahCard.classList.add('scale-100');
            }
        }, 10);
    }

    function closeModalTambahSlot() {
        if (!modalTambahSlot) return;
        modalTambahSlot.classList.remove('opacity-100');
        modalTambahSlot.classList.add('opacity-0');
        if (modalTambahCard) {
            modalTambahCard.classList.remove('scale-100');
            modalTambahCard.classList.add('scale-95');
        }
        setTimeout(() => {
            modalTambahSlot.classList.add('hidden');
        }, 200);
    }

    if (btnOpenTambahSlot) btnOpenTambahSlot.addEventListener('click', openModalTambahSlot);
    btnsCloseTambahSlot.forEach(btn => btn.addEventListener('click', closeModalTambahSlot));

    if (modalTambahSlot) {
        modalTambahSlot.addEventListener('click', (e) => {
            if (e.target === modalTambahSlot) closeModalTambahSlot();
        });
    }

    // Kirim form apa pun dan tampilkan hasilnya lewat PMMAlert.
    async function kirimForm(form, pesanSukses, judulSukses, onSukses) {
        const formData = new FormData(form);

        // Metode PUT/PATCH dikirim sebagai POST + _method (spoofing Laravel).
        const metodeAsli = (form.dataset.method || 'post').toLowerCase();
        const tujuan = form.dataset.url || form.action;

        if (metodeAsli !== 'post') {
            formData.append('_method', metodeAsli.toUpperCase());
        }

        try {
            const respons = await fetch(tujuan, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await respons.json().catch(() => ({}));

            if (!respons.ok) {
                // Laravel mengirim {message, errors:{field:[...]}} saat validasi gagal.
                const errorPertama = data.errors
                    ? Object.values(data.errors).flat()[0]
                    : null;
                notify.error(errorPertama || data.message || 'Data slot gagal disimpan.', {
                    title: 'Gagal Menyimpan',
                });

                return false;
            }

            notify.success(data.message || pesanSukses, { title: judulSukses });
            if (onSukses) onSukses();

            return true;
        } catch (err) {
            console.error('Error mengirim form slot:', err);
            notify.error('Gagal terhubung ke server. Silakan coba lagi.', {
                title: 'Koneksi Bermasalah',
            });

            return false;
        }
    }

    // Submit Form Tambah Slot
    if (formTambahSlot) {
        formTambahSlot.addEventListener('submit', async function (e) {
            e.preventDefault();

            const berhasil = await kirimForm(
                formTambahSlot,
                'Slot jadwal baru berhasil dibuat dan dibuka.',
                'Slot Tersimpan',
                () => {
                    closeModalTambahSlot();
                    reloadAfter();
                }
            );

            if (berhasil) {
                formTambahSlot.reset();
            }
        });
    }

    // Buka Modal Edit Slot
    function openModalEditSlot(data) {
        console.log(data)
        if (!modalEditSlot) return;

        const { jamMulai, jamSelesai, tanggal, kuota, terisi, status, url } = data;

        const infoJamEl = document.getElementById('modal-edit-info-jam');
        const infoTanggalEl = document.getElementById('modal-edit-info-tanggal');
        const inputKuota = document.getElementById('edit-kuota');
        const infoTerisi = document.getElementById('edit-terisi-info');
        const selectStatus = document.getElementById('edit-status');

        // Kedua input jam pada modal edit. Elemen <input> diisi lewat
        // .value, bukan .textContent (input tidak punya isi teks).
        const inputJamMulai = document.getElementById('jam_mulai');
        const inputJamSelesai = document.getElementById('jam_selesai');

        if (inputJamMulai) inputJamMulai.value = jamMulai || '';
        if (inputJamSelesai) inputJamSelesai.value = jamSelesai || '';

        if (infoJamEl) infoJamEl.textContent = (jamMulai && jamSelesai) ? `${jamMulai} - ${jamSelesai}` : '08:00 - 10:00';
        if (infoTanggalEl) infoTanggalEl.textContent = tanggal || '-';
        if (inputKuota) inputKuota.value = kuota || 1;
        if (infoTerisi) infoTerisi.textContent = `${terisi || 0} Mobil`;
        if (selectStatus && status) selectStatus.value = status;

        // URL aksi berasal dari route() di sisi server, bukan disusun di JS.
        if (formEditSlot && url) {
            formEditSlot.dataset.url = url;
            formEditSlot.dataset.method = 'put';
        }

        modalEditSlot.classList.remove('hidden');
        setTimeout(() => {
            modalEditSlot.classList.remove('opacity-0');
            modalEditSlot.classList.add('opacity-100');
            if (modalEditCard) {
                modalEditCard.classList.remove('scale-95');
                modalEditCard.classList.add('scale-100');
            }
        }, 10);
    }

    function closeModalEditSlot() {
        if (!modalEditSlot) return;
        modalEditSlot.classList.remove('opacity-100');
        modalEditSlot.classList.add('opacity-0');
        if (modalEditCard) {
            modalEditCard.classList.remove('scale-100');
            modalEditCard.classList.add('scale-95');
        }
        setTimeout(() => {
            modalEditSlot.classList.add('hidden');
        }, 200);
    }

    document.querySelectorAll('.btn-edit-slot').forEach(btn => {
        btn.addEventListener('click', function () {
            // data-jam-mulai / data-jam-selesai di HTML dibaca sebagai
            // dataset.jamMulai / dataset.jamSelesai (tanda hubung → huruf besar).
            const data = {
                jamMulai: this.dataset.jamMulai,
                jamSelesai: this.dataset.jamSelesai,
                tanggal: this.dataset.tanggal || '-',
                kuota: this.dataset.kuota || '1',
                terisi: this.dataset.terisi || '0',
                status: this.dataset.status || 'dibuka',
                url: this.dataset.url
            };
            openModalEditSlot(data);
        });
    });

    btnsCloseEditSlot.forEach(btn => btn.addEventListener('click', closeModalEditSlot));

    if (modalEditSlot) {
        modalEditSlot.addEventListener('click', (e) => {
            if (e.target === modalEditSlot) closeModalEditSlot();
        });
    }

    // Submit Form Edit Slot
    if (formEditSlot) {
        formEditSlot.addEventListener('submit', async function (e) {
            e.preventDefault();

            const berhasil = await kirimForm(
                formEditSlot,
                'Perubahan kuota & status slot berhasil disimpan.',
                'Perubahan Tersimpan',
                () => {
                    closeModalEditSlot();
                    reloadAfter();
                }
            );

            if (!berhasil) {
                return;
            }
        });
    }

    // Toggle Fast Status Buka/Tutup Slot
    // Hapus jadwal slot.
    // Untuk membuka kembali slot yang ditutup, gunakan tombol Edit
    // (kolom status di modal edit slot).
    document.querySelectorAll('.btn-hapus-slot').forEach(btn => {
        btn.addEventListener('click', async function () {
            const tujuan = this.dataset.url;
            const tanggal = this.dataset.tanggal || '';
            const jam = this.dataset.jam || '';
            const terisi = parseInt(this.dataset.terisi || '0', 10);

            if (!tujuan) {
                notify.error('Tautan aksi slot tidak ditemukan. Muat ulang halaman.', {
                    title: 'Gagal',
                });

                return;
            }

            // Sebutkan bila slot sudah pernah terisi, supaya admin sadar
            // ada riwayat booking yang terpengaruh.
            const pesan = terisi > 0
                ? `Slot ${jam} tanggal ${tanggal} akan dihapus dari jadwal. Slot ini pernah terisi ${terisi} booking, sehingga informasi jadwal pada pesanan lama akan kosong.`
                : `Slot ${jam} tanggal ${tanggal} akan dihapus dari jadwal secara permanen.`;

            const disetujui = await notify.confirm(pesan, {
                title: 'Hapus Jadwal Slot?',
                confirmText: 'Ya, Hapus',
                cancelText: 'Batal',
                danger: true,
            });

            if (!disetujui) {
                return;
            }

            try {
                const respons = await fetch(tujuan, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await respons.json().catch(() => ({}));

                if (!respons.ok) {
                    notify.error(data.message || 'Slot gagal dihapus.', {
                        title: 'Gagal',
                    });

                    return;
                }

                notify.success(data.message || 'Slot berhasil dihapus dari jadwal.', {
                    title: 'Slot Dihapus'
                });
                reloadAfter();
            } catch (err) {
                console.error('Error menghapus slot:', err);
                notify.error('Gagal terhubung ke server. Silakan coba lagi.', {
                    title: 'Koneksi Bermasalah',
                });
            }
        });
    });

    // Global Keydown ESC untuk menutup semua modal yang aktif
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (modalPesanan && !modalPesanan.classList.contains('hidden')) closeModalPesanan();
            if (modalTambahSlot && !modalTambahSlot.classList.contains('hidden')) closeModalTambahSlot();
            if (modalEditSlot && !modalEditSlot.classList.contains('hidden')) closeModalEditSlot();
        }
    });

    // ============================================================
    // 3. SUBMIT HANDLER FORM TERIMA & TOLAK PESANAN
    // ============================================================
    [formTerima, formTolak].forEach(form => {
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const isTerima = form.id === 'form-terima-modal';
            const formData = new FormData(form);

            if (isTerima) {
                const idMekanik = formData.get('id_mekanik');
                if (!idMekanik) {
                    notify.warning('Harap pilih mekanik bertugas terlebih dahulu.', {
                        title: 'Mekanik Belum Dipilih',
                    });
                    return;
                }
            } else {
                const keterangan = formData.get('keterangan');
                if (!keterangan || !keterangan.toString().trim()) {
                    notify.warning('Harap isi alasan penolakan pesanan.', {
                        title: 'Alasan Penolakan Kosong',
                    });
                    return;
                }
            }

            const actionUrl = form.action;

            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json().catch(() => ({ status: 'success' })))
            .then(data => {
                const judul = isTerima ? 'Pesanan Diterima' : 'Pesanan Ditolak';
                const pesan = isTerima
                    ? 'Pesanan berhasil diterima dan mekanik telah ditugaskan.'
                    : 'Pesanan telah ditolak dengan catatan penolakan.';

                notify[isTerima ? 'success' : 'info'](data.message || pesan, { title: judul });
                closeModalPesanan();
                reloadAfter();
            })
            .catch(err => {
                console.error('Error submitting form:', err);
                notify.error('Gagal mengirim respons ke server. Silakan coba lagi.', {
                    title: 'Koneksi Bermasalah',
                });
                closeModalPesanan();
            });
        });
    });

    // ============================================================
    // 6. AKSI ALUR PESANAN (Kerjakan → Selesaikan)
    // ============================================================
    /**
     * Kirim aksi PATCH ke server setelah dikonfirmasi pengguna.
     *
     * Dipakai bersama oleh tombol "Kerjakan" dan "Selesaikan" agar alur
     * konfirmasi dan penanganan galatnya konsisten.
     */
    async function kirimAksiPesanan(btn, opsi) {
        const tujuan = btn.dataset.url;
        const kode = btn.dataset.kode || 'pesanan ini';

        if (!tujuan) {
            notify.error('Tautan aksi tidak ditemukan. Muat ulang halaman.', { title: 'Gagal' });

            return;
        }

        const disetujui = await notify.confirm(opsi.pesan(kode), {
            title: opsi.judulKonfirmasi,
            confirmText: opsi.teksKonfirmasi,
            cancelText: 'Batal',
        });

        if (!disetujui) {
            return;
        }

        try {
            const respons = await fetch(tujuan, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await respons.json().catch(() => ({}));

            if (!respons.ok) {
                notify.error(data.message || opsi.pesanGagal, { title: 'Gagal' });

                return;
            }

            notify.success(data.message || opsi.pesanSukses, { title: opsi.judulSukses });
            reloadAfter();
        } catch (err) {
            console.error(opsi.logGalat, err);
            notify.error('Gagal terhubung ke server. Silakan coba lagi.', {
                title: 'Koneksi Bermasalah',
            });
        }
    }

    // Mulai kerjakan: mekanik mulai mengerjakan kendaraan.
    document.querySelectorAll('.btn-kerjakan-pesanan').forEach(btn => {
        btn.addEventListener('click', function () {
            kirimAksiPesanan(this, {
                pesan: (kode) => `Tandai ${kode} mulai dikerjakan? Status berubah menjadi "Dikerjakan" dan pelanggan menerima notifikasi.`,
                judulKonfirmasi: 'Mulai Kerjakan Pesanan?',
                teksKonfirmasi: 'Ya, Mulai Kerjakan',
                pesanSukses: 'Pesanan ditandai sedang dikerjakan.',
                judulSukses: 'Pengerjaan Dimulai',
                pesanGagal: 'Pesanan gagal ditandai dikerjakan.',
                logGalat: 'Error memulai pengerjaan:',
            });
        });
    });

    // Selesaikan: pengerjaan tuntas, kendaraan siap diambil.
    document.querySelectorAll('.btn-selesai-pesanan').forEach(btn => {
        btn.addEventListener('click', function () {
            kirimAksiPesanan(this, {
                pesan: (kode) => `Tandai ${kode} sebagai selesai? Notifikasi akan dikirim ke aplikasi mobile pelanggan.`,
                judulKonfirmasi: 'Selesaikan Pesanan?',
                teksKonfirmasi: 'Ya, Selesaikan',
                pesanSukses: 'Pesanan ditandai selesai.',
                judulSukses: 'Pesanan Selesai',
                pesanGagal: 'Pesanan gagal diselesaikan.',
                logGalat: 'Error menyelesaikan pesanan:',
            });
        });
    });

    // ============================================================
    // 5. CRUD MASTER DATA MEKANIK
    // ============================================================
    const modalTambahMekanik = document.getElementById('modal-tambah-mekanik');
    const modalTambahMekanikCard = document.getElementById('modal-tambah-mekanik-card');
    const formTambahMekanik = document.getElementById('form-tambah-mekanik');

    const modalEditMekanik = document.getElementById('modal-edit-mekanik');
    const modalEditMekanikCard = document.getElementById('modal-edit-mekanik-card');
    const formEditMekanik = document.getElementById('form-edit-mekanik');

    /**
     * Buka/tutup modal dengan animasi halus (pola sama seperti modal slot).
     */
    function bukaModal(modal, kartu) {
        if (!modal) return;
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            if (kartu) {
                kartu.classList.remove('scale-95');
                kartu.classList.add('scale-100');
            }
        }, 10);
    }

    function tutupModal(modal, kartu) {
        if (!modal) return;
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        if (kartu) {
            kartu.classList.remove('scale-100');
            kartu.classList.add('scale-95');
        }
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    // --- Modal Tambah ---
    document.querySelectorAll('.btn-open-tambah-mekanik, #btn-open-tambah-mekanik').forEach(btn => {
        btn.addEventListener('click', () => {
            if (formTambahMekanik) formTambahMekanik.reset();
            bukaModal(modalTambahMekanik, modalTambahMekanikCard);
        });
    });

    document.querySelectorAll('.btn-close-tambah-mekanik').forEach(btn => {
        btn.addEventListener('click', () => tutupModal(modalTambahMekanik, modalTambahMekanikCard));
    });

    if (modalTambahMekanik) {
        modalTambahMekanik.addEventListener('click', (e) => {
            if (e.target === modalTambahMekanik) tutupModal(modalTambahMekanik, modalTambahMekanikCard);
        });
    }

    if (formTambahMekanik) {
        formTambahMekanik.addEventListener('submit', async (e) => {
            e.preventDefault();

            const berhasil = await kirimForm(
                formTambahMekanik,
                'Mekanik baru berhasil ditambahkan.',
                'Mekanik Tersimpan',
                () => {
                    tutupModal(modalTambahMekanik, modalTambahMekanikCard);
                    reloadAfter();
                }
            );

            if (berhasil) formTambahMekanik.reset();
        });
    }

    // --- Modal Edit ---
    function bukaModalEditMekanik(data) {
      //  console.log(data)
        if (!modalEditMekanik) return;

        const elNama = document.getElementById('edit-nama-mekanik');
        const elSpesialis = document.getElementById('edit-spesialis');
        const elNoHp = document.getElementById('edit-no-hp-mekanik');
        const elAktif = document.getElementById('edit-aktif');

        if (elNama) elNama.value = data.nama || '';
        if (elSpesialis) elSpesialis.value = data.spesialis || '';
        if (elNoHp) elNoHp.value = data.noHp || '';
        if (elAktif) elAktif.checked = data.aktif === '1';

        // URL aksi dari route() di sisi server, bukan disusun di JS.
        if (formEditMekanik && data.url) {
            formEditMekanik.dataset.url = data.url;
            formEditMekanik.dataset.method = 'put';
        }

        bukaModal(modalEditMekanik, modalEditMekanikCard);
    }

    document.querySelectorAll('.btn-edit-mekanik').forEach(btn => {
        btn.addEventListener('click', function () {
            bukaModalEditMekanik({
                nama: this.dataset.nama,
                spesialis: this.dataset.spesialis,
                noHp: this.dataset.noHp,
                aktif: this.dataset.aktif,
                url: this.dataset.url
            });
        });
    });

    document.querySelectorAll('.btn-close-edit-mekanik').forEach(btn => {
        btn.addEventListener('click', () => tutupModal(modalEditMekanik, modalEditMekanikCard));
    });

    if (modalEditMekanik) {
        modalEditMekanik.addEventListener('click', (e) => {
            if (e.target === modalEditMekanik) tutupModal(modalEditMekanik, modalEditMekanikCard);
        });
    }

    if (formEditMekanik) {
        formEditMekanik.addEventListener('submit', async (e) => {
            e.preventDefault();

            await kirimForm(
                formEditMekanik,
                'Data mekanik berhasil diperbarui.',
                'Perubahan Tersimpan',
                () => {
                    tutupModal(modalEditMekanik, modalEditMekanikCard);
                    reloadAfter();
                }
            );
        });
    }

    // --- Aktif / Nonaktifkan ---
    document.querySelectorAll('.btn-toggle-mekanik').forEach(btn => {
        btn.addEventListener('click', async function () {
            const tujuan = this.dataset.url;
            const nama = this.dataset.nama || 'mekanik ini';
            const akanNonaktif = this.dataset.aktif === '1';

            if (!tujuan) {
                notify.error('Tautan aksi tidak ditemukan. Muat ulang halaman.', { title: 'Gagal' });

                return;
            }

            const pesan = akanNonaktif
                ? `${nama} tidak akan muncul pada pilihan penugasan saat menerima pesanan. Riwayat pengerjaannya tetap tersimpan.`
                : `${nama} akan muncul kembali pada pilihan penugasan mekanik.`;

            const disetujui = await notify.confirm(pesan, {
                title: akanNonaktif ? 'Nonaktifkan Mekanik?' : 'Aktifkan Mekanik?',
                confirmText: akanNonaktif ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
                cancelText: 'Batal',
            });

            if (!disetujui) return;

            try {
                const respons = await fetch(tujuan, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await respons.json().catch(() => ({}));

                if (!respons.ok) {
                    notify.error(data.message || 'Status mekanik gagal diubah.', { title: 'Gagal' });

                    return;
                }

                notify.success(data.message || 'Status mekanik berhasil diubah.', { title: 'Status Diperbarui' });
                reloadAfter();
            } catch (err) {
                console.error('Error toggle mekanik:', err);
                notify.error('Gagal terhubung ke server. Silakan coba lagi.', { title: 'Koneksi Bermasalah' });
            }
        });
    });

    // --- Hapus (soft delete) ---
    document.querySelectorAll('.btn-hapus-mekanik').forEach(btn => {
        btn.addEventListener('click', async function () {
            const tujuan = this.dataset.url;
            const nama = this.dataset.nama || 'mekanik ini';
            const totalPenugasan = parseInt(this.dataset.totalPenugasan || '0', 10);

            if (!tujuan) {
                notify.error('Tautan aksi tidak ditemukan. Muat ulang halaman.', { title: 'Gagal' });

                return;
            }

            // Jelaskan konsekuensinya, terutama bahwa riwayat tetap aman.
            const pesan = totalPenugasan > 0
                ? `${nama} akan dihapus dari daftar mekanik aktif. ${totalPenugasan} riwayat pengerjaannya TETAP tersimpan dan nama beliau masih tampil pada nota pelanggan lama.`
                : `${nama} akan dihapus dari daftar mekanik. Tindakan ini tidak dapat dibatalkan dari halaman ini.`;

            const disetujui = await notify.confirm(pesan, {
                title: 'Hapus Mekanik?',
                confirmText: 'Ya, Hapus',
                cancelText: 'Batal',
                danger: true,
            });

            if (!disetujui) return;

            try {
                const respons = await fetch(tujuan, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await respons.json().catch(() => ({}));

                if (!respons.ok) {
                    notify.error(data.message || 'Mekanik gagal dihapus.', { title: 'Gagal' });

                    return;
                }

                notify.success(data.message || 'Mekanik berhasil dihapus.', { title: 'Mekanik Dihapus' });
                reloadAfter();
            } catch (err) {
                console.error('Error menghapus mekanik:', err);
                notify.error('Gagal terhubung ke server. Silakan coba lagi.', { title: 'Koneksi Bermasalah' });
            }
        });
    });

    // ESC menutup modal mekanik juga
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;

        if (modalTambahMekanik && !modalTambahMekanik.classList.contains('hidden')) {
            tutupModal(modalTambahMekanik, modalTambahMekanikCard);
        }
        if (modalEditMekanik && !modalEditMekanik.classList.contains('hidden')) {
            tutupModal(modalEditMekanik, modalEditMekanikCard);
        }
    });

    // ============================================================
    // 6. MASTER DATA SPAREPART & PEMAKAIANNYA PADA PESANAN
    // ============================================================
    const modalTambahSparepart = document.getElementById('modal-tambah-sparepart');
    const modalTambahSparepartCard = document.getElementById('modal-tambah-sparepart-card');
    const formTambahSparepart = document.getElementById('form-tambah-sparepart');

    const modalEditSparepart = document.getElementById('modal-edit-sparepart');
    const modalEditSparepartCard = document.getElementById('modal-edit-sparepart-card');
    const formEditSparepart = document.getElementById('form-edit-sparepart');

    // --- Modal Tambah Sparepart ---
    document.querySelectorAll('.btn-open-tambah-sparepart, #btn-open-tambah-sparepart').forEach(btn => {
        btn.addEventListener('click', () => {
            if (formTambahSparepart) formTambahSparepart.reset();
            bukaModal(modalTambahSparepart, modalTambahSparepartCard);
        });
    });

    document.querySelectorAll('.btn-close-tambah-sparepart').forEach(btn => {
        btn.addEventListener('click', () => tutupModal(modalTambahSparepart, modalTambahSparepartCard));
    });

    if (modalTambahSparepart) {
        modalTambahSparepart.addEventListener('click', (e) => {
            if (e.target === modalTambahSparepart) tutupModal(modalTambahSparepart, modalTambahSparepartCard);
        });
    }

    if (formTambahSparepart) {
        formTambahSparepart.addEventListener('submit', async (e) => {
            e.preventDefault();

            const berhasil = await kirimForm(
                formTambahSparepart,
                'Sparepart baru berhasil ditambahkan.',
                'Sparepart Tersimpan',
                () => {
                    tutupModal(modalTambahSparepart, modalTambahSparepartCard);
                    reloadAfter();
                }
            );

            if (berhasil) formTambahSparepart.reset();
        });
    }

    // --- Modal Edit Sparepart ---
    function bukaModalEditSparepart(data) {
        if (!modalEditSparepart) return;

        const elNama = document.getElementById('edit-nama-sparepart');
        const elSatuan = document.getElementById('edit-satuan');
        const elHarga = document.getElementById('edit-harga');
        const elStok = document.getElementById('edit-stok');
        const elStokMinimal = document.getElementById('edit-stok-minimal');

        if (elNama) elNama.value = data.nama || '';
        if (elSatuan) elSatuan.value = data.satuan || '';
        if (elHarga) elHarga.value = data.harga || 0;
        if (elStok) elStok.value = data.stok || 0;
        if (elStokMinimal) elStokMinimal.value = data.stokMinimal || 0;

        // URL aksi dari route() di sisi server, bukan disusun di JS.
        if (formEditSparepart && data.url) {
            formEditSparepart.dataset.url = data.url;
            formEditSparepart.dataset.method = 'put';
        }

        bukaModal(modalEditSparepart, modalEditSparepartCard);
    }

    document.querySelectorAll('.btn-edit-sparepart').forEach(btn => {
        btn.addEventListener('click', function () {
            bukaModalEditSparepart({
                nama: this.dataset.nama,
                satuan: this.dataset.satuan,
                harga: this.dataset.harga,
                stok: this.dataset.stok,
                stokMinimal: this.dataset.stokMinimal,
                url: this.dataset.url
            });
        });
    });

    document.querySelectorAll('.btn-close-edit-sparepart').forEach(btn => {
        btn.addEventListener('click', () => tutupModal(modalEditSparepart, modalEditSparepartCard));
    });

    if (modalEditSparepart) {
        modalEditSparepart.addEventListener('click', (e) => {
            if (e.target === modalEditSparepart) tutupModal(modalEditSparepart, modalEditSparepartCard);
        });
    }

    if (formEditSparepart) {
        formEditSparepart.addEventListener('submit', async (e) => {
            e.preventDefault();

            await kirimForm(
                formEditSparepart,
                'Data sparepart berhasil diperbarui.',
                'Perubahan Tersimpan',
                () => {
                    tutupModal(modalEditSparepart, modalEditSparepartCard);
                    reloadAfter();
                }
            );
        });
    }

    // --- Hapus Sparepart (master data) ---
    document.querySelectorAll('.btn-hapus-sparepart').forEach(btn => {
        btn.addEventListener('click', async function () {
            const tujuan = this.dataset.url;
            const nama = this.dataset.nama || 'sparepart ini';

            if (!tujuan) {
                notify.error('Tautan aksi tidak ditemukan. Muat ulang halaman.', { title: 'Gagal' });

                return;
            }

            const disetujui = await notify.confirm(
                `${nama} akan dihapus dari master data. Sparepart yang belum pernah dipakai tidak memengaruhi nota mana pun.`,
                {
                    title: 'Hapus Sparepart?',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    danger: true,
                }
            );

            if (!disetujui) return;

            try {
                const respons = await fetch(tujuan, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await respons.json().catch(() => ({}));

                if (!respons.ok) {
                    notify.error(data.message || 'Sparepart gagal dihapus.', { title: 'Gagal' });

                    return;
                }

                notify.success(data.message || 'Sparepart berhasil dihapus.', { title: 'Sparepart Dihapus' });
                reloadAfter();
            } catch (err) {
                console.error('Error menghapus sparepart:', err);
                notify.error('Gagal terhubung ke server. Silakan coba lagi.', { title: 'Koneksi Bermasalah' });
            }
        });
    });

    // --- Catat pemakaian sparepart pada pesanan ---
    const formCatatSparepart = document.getElementById('form-catat-sparepart');
    if (formCatatSparepart) {
        formCatatSparepart.addEventListener('submit', async (e) => {
            e.preventDefault();

            await kirimForm(
                formCatatSparepart,
                'Pemakaian sparepart berhasil dicatat.',
                'Pemakaian Dicatat',
                reloadAfter
            );
        });
    }

    // --- Batalkan pemakaian (stok dikembalikan) ---
    document.querySelectorAll('.btn-hapus-pemakaian').forEach(btn => {
        btn.addEventListener('click', async function () {
            const tujuan = this.dataset.url;
            const nama = this.dataset.nama || 'sparepart ini';
            const jumlah = this.dataset.jumlah || '0';
            const satuan = this.dataset.satuan || '';

            if (!tujuan) {
                notify.error('Tautan aksi tidak ditemukan. Muat ulang halaman.', { title: 'Gagal' });

                return;
            }

            const disetujui = await notify.confirm(
                `Pemakaian ${nama} sebanyak ${jumlah} ${satuan} akan dibatalkan. Stok akan dikembalikan ke persediaan dan total biaya pesanan menyesuaikan.`,
                {
                    title: 'Batalkan Pemakaian?',
                    confirmText: 'Ya, Batalkan',
                    cancelText: 'Tidak',
                    danger: true,
                }
            );

            if (!disetujui) return;

            try {
                const respons = await fetch(tujuan, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await respons.json().catch(() => ({}));

                if (!respons.ok) {
                    notify.error(data.message || 'Pemakaian gagal dibatalkan.', { title: 'Gagal' });

                    return;
                }

                notify.success(data.message || 'Pemakaian dibatalkan dan stok dikembalikan.', {
                    title: 'Pemakaian Dibatalkan'
                });
                reloadAfter();
            } catch (err) {
                console.error('Error membatalkan pemakaian:', err);
                notify.error('Gagal terhubung ke server. Silakan coba lagi.', { title: 'Koneksi Bermasalah' });
            }
        });
    });

    // ESC menutup modal sparepart juga
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;

        if (modalTambahSparepart && !modalTambahSparepart.classList.contains('hidden')) {
            tutupModal(modalTambahSparepart, modalTambahSparepartCard);
        }
        if (modalEditSparepart && !modalEditSparepart.classList.contains('hidden')) {
            tutupModal(modalEditSparepart, modalEditSparepartCard);
        }
    });

    // ============================================================
    // 4. CAROUSEL INFORMASI FITUR LOGIN
    // ============================================================
    const carouselContainer = document.getElementById('login-carousel');
    if (carouselContainer) {
        const slides = carouselContainer.querySelectorAll('[data-carousel-slide]');
        const dots = carouselContainer.querySelectorAll('[data-carousel-dot]');
        const indexEl = document.getElementById('carousel-current-index');
        let currentIndex = 0;
        let timer = null;

        function goToSlide(index) {
            slides.forEach((slide, i) => {
                if (i === index) {
                    slide.classList.remove('opacity-0', 'pointer-events-none', 'translate-x-8', '-translate-x-8');
                    slide.classList.add('opacity-100', 'pointer-events-auto', 'translate-x-0');
                } else {
                    slide.classList.remove('opacity-100', 'pointer-events-auto', 'translate-x-0');
                    slide.classList.add('opacity-0', 'pointer-events-none', i < index ? '-translate-x-8' : 'translate-x-8');
                }
            });

            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.remove('w-2', 'bg-white/30');
                    dot.classList.add('w-6', 'bg-[#E5A919]');
                } else {
                    dot.classList.remove('w-6', 'bg-[#E5A919]');
                    dot.classList.add('w-2', 'bg-white/30');
                }
            });

            if (indexEl) {
                indexEl.textContent = String(index + 1).padStart(2, '0');
            }

            currentIndex = index;
        }

        function nextSlide() {
            const next = (currentIndex + 1) % slides.length;
            goToSlide(next);
        }

        function startTimer() {
            stopTimer();
            timer = setInterval(nextSlide, 4000);
        }

        function stopTimer() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => {
                goToSlide(i);
                startTimer();
            });
        });

        carouselContainer.addEventListener('mouseenter', stopTimer);
        carouselContainer.addEventListener('mouseleave', startTimer);

        // Inisialisasi slide pertama
        goToSlide(0);
        startTimer();
    }

});
