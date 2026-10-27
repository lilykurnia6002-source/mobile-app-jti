<!-- ============================================================
     POPUP / ALERT GLOBAL — PMM AutoCare
     Toast notifikasi ringan: muncul dengan sedikit shake halus,
     bertahan 4 detik, lalu menghilang sendiri.

     Include sekali per halaman (tulis direktif include seperti biasa,
     tanpa tanda kurung siku di dalam komentar ini).

     Pemakaian dari mana saja (termasuk resources/js/app.js):
         PMMAlert.success('Slot berhasil dibuka', { title: 'Berhasil Disimpan' })
         PMMAlert.error('Gagal menyimpan slot')
         PMMAlert.warning('Kuota tersisa 1 slot')
         PMMAlert.info('Data dimuat ulang')
         PMMAlert.dismissAll()
         PMMAlert.setDuration(6000)          // ubah durasi global (ms)

     Konfirmasi (pengganti confirm() bawaan browser):
         const ok = await PMMAlert.confirm('Tutup slot ini?', {
             title: 'Konfirmasi Penutupan',
             confirmText: 'Ya, Tutup',
             cancelText: 'Batal',
             danger: true,
         })
         if (ok) { ... }

     Varian: success · error · warning · info
     Durasi bawaan 4 detik (dapat diubah lewat meta pmm-alert-config).
     Kartu otomatis berhenti menghitung saat kursor berhenti di atasnya.

     Pesan flash Laravel otomatis tampil bila halaman mencetak:
         <span data-pmm-flash="success" data-pmm-flash-title="Berhasil"
               data-pmm-flash-text="{{ session('success') }}"></span>
============================================================ -->
<meta name="pmm-alert-config" content="{{ json_encode(['duration' => 4000]) }}">

<script>
    window.PMMAlert = (function () {
        'use strict';

        /** Durasi tampil bawaan (ms) — sinkron dengan --animate-pmm-progress di app.css */
        const DEFAULT_DURATION = 4000;
        const EXIT_DURATION = 200;

        /** Satu-satunya sumber kebenaran gaya tiap varian */
        const VARIANTS = {
            success: {
                label: 'Berhasil',
                icon: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                accent: 'bg-emerald-500',
                iconWrap: 'bg-emerald-50 text-emerald-600 ring-emerald-100',
                title: 'text-emerald-700',
                progress: 'bg-emerald-500',
            },
            error: {
                label: 'Gagal',
                icon: 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                accent: 'bg-rose-500',
                iconWrap: 'bg-rose-50 text-rose-600 ring-rose-100',
                title: 'text-rose-700',
                progress: 'bg-rose-500',
            },
            warning: {
                label: 'Perhatian',
                icon: 'M12 9v3.75m0 3.75h.008M10.34 3.94l-8.4 14.4A1.5 1.5 0 003.24 21h17.52a1.5 1.5 0 001.3-2.66l-8.4-14.4a1.5 1.5 0 00-2.6 0z',
                accent: 'bg-amber-500',
                iconWrap: 'bg-amber-50 text-amber-600 ring-amber-100',
                title: 'text-amber-700',
                progress: 'bg-amber-500',
            },
            info: {
                label: 'Informasi',
                icon: 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
                accent: 'bg-[#1F3864]',
                iconWrap: 'bg-slate-100 text-[#1F3864] ring-slate-200',
                title: 'text-[#1F3864]',
                progress: 'bg-[#1F3864]',
            },
        };

        const MAX_VISIBLE = 4;
        let container = null;

        /**
         * Escape teks agar pesan dinamis (mis. dari respons server) tidak
         * disisipkan sebagai HTML mentah.
         *
         * @param {*} value
         * @returns {string}
         */
        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        /**
         * Ambil elemen statis bila halaman menyediakan kaitan sendiri.
         * Bila tidak ada, buat wadah sekali saja secara lazy.
         *
         * @returns {HTMLElement}
         */
        function getContainer() {
            if (container && document.body.contains(container)) {
                return container;
            }

            container = document.getElementById('pmm-alert-container');

            if (!container) {
                container = document.createElement('div');
                container.id = 'pmm-alert-container';
                container.setAttribute('role', 'region');
                container.setAttribute('aria-label', 'Notifikasi sistem');
                container.className =
                    'fixed top-20 right-4 left-4 sm:left-auto z-[100] flex flex-col items-end gap-2.5 pointer-events-none';
                document.body.appendChild(container);
            }

            return container;
        }

        /**
         * Pastikan hanya sejumlah kartu terbatas tampil bersamaan.
         */
        function trimStack() {
            const root = getContainer();
            const cards = root.querySelectorAll('[data-pmm-alert]');

            for (let i = 0; i < cards.length - MAX_VISIBLE; i++) {
                cards[i].remove();
            }
        }

        /**
         * Tutup satu kartu popup dengan animasi keluar.
         *
         * @param {HTMLElement} card
         */
        function closeCard(card) {
            if (!card || card.dataset.closing === 'true') {
                return;
            }

            card.dataset.closing = 'true';
            card.classList.remove('animate-pmm-toast-in');

            const slot = card.querySelector('[data-pmm-alert-slot]');
            if (slot) {
                slot.classList.remove('animate-pmm-shake');
            }

            card.classList.add('animate-pmm-toast-out');
            card.classList.remove('pointer-events-auto');
            card.classList.add('pointer-events-none');

            window.setTimeout(() => card.remove(), EXIT_DURATION);
        }

        /**
         * Susun satu kartu popup untuk varian tertentu.
         *
         * @param {string} variant
         * @param {string} message
         * @param {object} options - { title?: string, duration?: number }
         * @returns {HTMLElement}
         */
        function buildCard(variant, message, options) {
            const tone = VARIANTS[variant] || VARIANTS.info;
            const title = options.title || tone.label;
            const duration = Number(options.duration) > 0 ? Number(options.duration) : DEFAULT_DURATION;

            const card = document.createElement('div');
            card.dataset.pmmAlert = variant;
            card.setAttribute('role', variant === 'error' ? 'alert' : 'status');
            card.setAttribute('aria-live', variant === 'error' ? 'assertive' : 'polite');
            card.className =
                'pointer-events-auto relative w-full sm:w-[20rem] max-w-[22rem] overflow-hidden ' +
                'bg-white border border-slate-200/90 rounded-[5px] shadow-lg shadow-slate-900/5 ' +
                'animate-pmm-toast-in';

            card.innerHTML = `
                <span class="absolute inset-y-0 left-0 w-1 ${tone.accent}" aria-hidden="true"></span>
                <div data-pmm-alert-slot class="flex items-start gap-2.5 pl-3.5 pr-2.5 py-2.5">
                    <span class="shrink-0 w-7 h-7 rounded-[5px] ring-1 flex items-center justify-center ${tone.iconWrap}" aria-hidden="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="${tone.icon}"></path>
                        </svg>
                    </span>

                    <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-bold leading-snug ${tone.title}">${escapeHtml(title)}</p>
                        <p class="text-[11px] text-slate-500 leading-snug mt-0.5 break-words">${escapeHtml(message)}</p>
                    </div>

                    <button type="button" data-pmm-alert-close
                        class="shrink-0 -mt-0.5 -mr-0.5 p-1 rounded-[5px] text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#1F3864]"
                        aria-label="Tutup notifikasi">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <span class="block h-[2px] bg-slate-100" aria-hidden="true">
                    <span class="block h-full ${tone.progress} animate-pmm-progress" style="animation-duration:${duration}ms"></span>
                </span>
            `;

            card.querySelector('[data-pmm-alert-close]')?.addEventListener('click', () => closeCard(card));

            // Getaran halus di awal, sekali saja, setelah kartu mulai masuk.
            const slot = card.querySelector('[data-pmm-alert-slot]');
            if (slot) {
                slot.addEventListener(
                    'animationend',
                    () => slot.classList.remove('animate-pmm-shake'),
                    { once: true },
                );
            }

            // Tahan saat kursor berhenti di atas kartu, lanjutkan setelah keluar.
            let remaining = duration;
            let startedAt = 0;
            let timer = null;

            const dismiss = () => closeCard(card);

            const arm = (ms) => {
                window.clearTimeout(timer);
                startedAt = Date.now();
                timer = window.setTimeout(dismiss, ms);
            };

            card.addEventListener('mouseenter', () => {
                if (card.dataset.closing === 'true') {
                    return;
                }

                window.clearTimeout(timer);
                remaining = Math.max(600, remaining - (Date.now() - startedAt));

                const bar = card.querySelector('.animate-pmm-progress');
                if (bar) {
                    bar.style.animationPlayState = 'paused';
                }
            });

            card.addEventListener('mouseleave', () => {
                if (card.dataset.closing === 'true') {
                    return;
                }

                const bar = card.querySelector('.animate-pmm-progress');
                if (bar) {
                    bar.style.animationPlayState = 'running';
                }

                arm(remaining);
            });

            arm(duration);

            return card;
        }

        /**
         * Tampilkan popup.
         *
         * @param {string} variant success|error|warning|info
         * @param {string} message
         * @param {object} [options] - { title?: string, duration?: number }
         * @returns {HTMLElement|null}
         */
        function show(variant, message, options = {}) {
            if (typeof document === 'undefined' || !document.body) {
                return null;
            }

            const root = getContainer();
            const card = buildCard(variant, message, options);

            root.appendChild(card);
            trimStack();

            return card;
        }

        /**
         * Popup konfirmasi bergaya aplikasi, pengganti confirm() bawaan browser.
         * Mengembalikan Promise<boolean>.
         *
         * @param {string} message
         * @param {object} [options] - { title?, confirmText?, cancelText?, danger? }
         * @returns {Promise<boolean>}
         */
        function confirmDialog(message, options = {}) {
            const tone = options.danger ? VARIANTS.error : VARIANTS.info;

            return new Promise((resolve) => {
                const overlay = document.createElement('div');
                overlay.className =
                    'fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm';
                overlay.setAttribute('role', 'dialog');
                overlay.setAttribute('aria-modal', 'true');

                overlay.innerHTML = `
                    <div data-pmm-confirm-card class="w-full max-w-sm bg-white rounded-[5px] border border-slate-200 shadow-2xl overflow-hidden animate-pmm-toast-in">
                        <div data-pmm-alert-slot class="flex items-start gap-3 p-4">
                            <span class="shrink-0 w-8 h-8 rounded-[5px] ring-1 flex items-center justify-center ${tone.iconWrap}" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="${tone.icon}"></path>
                                </svg>
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-bold leading-snug ${tone.title}">${escapeHtml(options.title || 'Konfirmasi')}</p>
                                <p class="text-[11px] text-slate-500 leading-snug mt-1 break-words">${escapeHtml(message)}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-2 px-4 py-3 border-t border-slate-100 bg-slate-50/70">
                            <button type="button" data-pmm-confirm="cancel"
                                class="px-3.5 py-1.5 rounded-[5px] border border-slate-200 bg-white text-slate-600 text-[12px] font-semibold hover:bg-slate-100 transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-slate-400">
                                ${escapeHtml(options.cancelText || 'Batal')}
                            </button>
                            <button type="button" data-pmm-confirm="ok"
                                class="px-3.5 py-1.5 rounded-[5px] text-white text-[12px] font-bold transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#1F3864] ${options.danger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-[#1F3864] hover:bg-[#152747]'}">
                                ${escapeHtml(options.confirmText || 'Ya, Lanjutkan')}
                            </button>
                        </div>
                    </div>
                `;

                const previousFocused = document.activeElement;
                const finish = (result) => {
                    overlay.remove();
                    document.removeEventListener('keydown', onKeydown);
                    previousFocused?.focus?.({ preventScroll: true });
                    resolve(result);
                };

                function onKeydown(event) {
                    if (event.key === 'Escape') {
                        event.preventDefault();
                        finish(false);
                    }
                }

                overlay.querySelector('[data-pmm-confirm="cancel"]')?.addEventListener('click', () => finish(false));
                overlay.querySelector('[data-pmm-confirm="ok"]')?.addEventListener('click', () => finish(true));
                overlay.addEventListener('click', (event) => {
                    if (event.target === overlay) {
                        finish(false);
                    }
                });
                document.addEventListener('keydown', onKeydown);

                document.body.appendChild(overlay);
                overlay.querySelector('[data-pmm-confirm="ok"]')?.focus({ preventScroll: true });
            });
        }

        /**
         * Baca durasi bawaan dari halaman (bila disetel lewat meta config).
         *
         * @returns {number}
         */
        function resolveConfiguredDuration() {
            const meta = document.querySelector('meta[name="pmm-alert-config"]');

            if (!meta) {
                return DEFAULT_DURATION;
            }

            try {
                const parsed = JSON.parse(meta.getAttribute('content') || '{}');

                return Number(parsed.duration) > 0 ? Number(parsed.duration) : DEFAULT_DURATION;
            } catch (error) {
                return DEFAULT_DURATION;
            }
        }

        /** Durasi tampil aktif; dihormati saat panggilan tidak menyetel durasi sendiri. */
        let activeDuration = null;

        /**
         * Durasi yang dipakai bila pemanggil tidak menyetelnya.
         *
         * @returns {number}
         */
        function currentDuration() {
            if (activeDuration === null) {
                activeDuration = resolveConfiguredDuration();
            }

            return activeDuration;
        }

        /**
         * Bungkus pemanggilan varian agar selalu memakai durasi aktif.
         *
         * @param {string} variant
         * @returns {(message: string, options?: object) => HTMLElement|null}
         */
        function variantCaller(variant) {
            return (message, options = {}) =>
                show(variant, message, { duration: currentDuration(), ...options });
        }

        /**
         * Tampilkan pesan flash Laravel saat halaman selesai dimuat.
         *
         * Cetak penanda berikut di halaman (biasanya dari layout):
         *     <span data-pmm-flash="success"
         *           data-pmm-flash-title="Berhasil"
         *           data-pmm-flash-text="{{ session('success') }}"></span>
         *
         * Varian yang dikenali: success · error · warning · info.
         */
        function flushPendingMessages() {
            document.querySelectorAll('[data-pmm-flash]').forEach((el) => {
                const variant = VARIANTS[el.dataset.pmmFlash] ? el.dataset.pmmFlash : 'info';
                const message = el.dataset.pmmFlashText;

                if (message) {
                    variantCaller(variant)(message, { title: el.dataset.pmmFlashTitle });
                }

                el.remove();
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', flushPendingMessages);
        } else {
            flushPendingMessages();
        }

        return {
            show: (variant, message, options = {}) =>
                show(variant, message, { duration: currentDuration(), ...options }),
            success: variantCaller('success'),
            error: variantCaller('error'),
            warning: variantCaller('warning'),
            info: variantCaller('info'),
            confirm: (message, options = {}) => confirmDialog(message, options),
            dismissAll: () => {
                getContainer()
                    .querySelectorAll('[data-pmm-alert]')
                    .forEach((card) => closeCard(card));
            },
            setDuration: (ms) => {
                if (Number(ms) > 0) {
                    activeDuration = Number(ms);
                }
            },
        };
    })();
</script>