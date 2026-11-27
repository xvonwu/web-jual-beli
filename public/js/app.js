// Interaksi kecil tanpa framework: menu, pencarian, dropdown akun, dropdown Belanja.
// Setiap tombol toggle menyimpan status di aria-expanded supaya pembaca layar tahu menu terbuka/tertutup.
(function () {
    const setOpen = function (btn, panel, open) {
        if (!btn || !panel) return;
        panel.classList.toggle('open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    const nav = document.getElementById('mainNav');
    const navBtn = document.querySelector('[data-toggle-nav]');
    const bar = document.getElementById('searchBar');
    const searchBtn = document.querySelector('[data-toggle-search]');
    const dd = document.querySelector('.dd');
    const ddBtn = dd && dd.querySelector('.dd-toggle');
    const um = document.querySelector('.user-menu');
    const umBtn = um && um.querySelector('[data-toggle-user]');

    document.addEventListener('click', function (e) {
        if (e.target.closest('[data-toggle-search]')) {
            const open = !bar.classList.contains('open');
            setOpen(searchBtn, bar, open);
            if (open) bar.querySelector('input').focus();
        }
        if (e.target.closest('[data-toggle-nav]')) setOpen(navBtn, nav, !nav.classList.contains('open'));
        if (um) {
            if (e.target.closest('[data-toggle-user]')) setOpen(umBtn, um, !um.classList.contains('open'));
            else if (!e.target.closest('.user-menu')) setOpen(umBtn, um, false);
        }
        if (dd) {
            if (e.target.closest('.dd-toggle')) setOpen(ddBtn, dd, !dd.classList.contains('open'));
            else if (!e.target.closest('.dd')) setOpen(ddBtn, dd, false);
        }
    });

    // Esc menutup dropdown yang terbuka dan mengembalikan fokus ke tombolnya
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        [[ddBtn, dd], [umBtn, um], [searchBtn, bar], [navBtn, nav]].forEach(function (pair) {
            const btn = pair[0], panel = pair[1];
            if (btn && panel && panel.classList.contains('open') && panel.contains(document.activeElement)) {
                setOpen(btn, panel, false); btn.focus();
            }
        });
        if (dd && dd.classList.contains('open')) setOpen(ddBtn, dd, false);
        if (um && um.classList.contains('open')) setOpen(umBtn, um, false);
    });

    // Fokus keyboard keluar dari dropdown = dropdown ditutup
    [[ddBtn, dd], [umBtn, um]].forEach(function (pair) {
        const btn = pair[0], panel = pair[1];
        if (!panel) return;
        panel.addEventListener('focusout', function (e) {
            if (e.relatedTarget && !panel.contains(e.relatedTarget)) setOpen(btn, panel, false);
        });
    });
})();

// Tab cara pengiriman di keranjang (Ambil Langsung / GOJEK / Grab / JNE / J&T)
(function () {
    const form = document.getElementById('shippingForm');
    if (!form) return;
    const hidden = form.querySelector('[name=jenisongkir]');
    const notes = {
        pickup: 'Ambil langsung di kantor. Harap konfirmasi dulu sebelum datang. Bawa invoice & bukti transfer (bila sudah transfer).',
        gojek: 'Dikirim via GOJEK. Ongkos kirim dibayar langsung ke driver.',
        grab: 'Dikirim via Grab. Ongkos kirim dibayar langsung ke driver.',
        jne: 'JNE: tarif sesuai provinsi tujuan x berat paket.',
        jnt: 'J&T: tarif sesuai provinsi tujuan x berat paket.'
    };
    function show(type) {
        hidden.value = type;
        form.querySelectorAll('.tab').forEach(t => t.classList.toggle('active', t.dataset.type === type));
        form.querySelectorAll('[data-show]').forEach(el => {
            el.style.display = el.dataset.show.split(' ').includes(type) ? '' : 'none';
        });
        form.querySelector('.tab-note').textContent = notes[type];
        // radio cash hanya untuk pickup
        const cash = document.querySelector('[data-cash]');
        if (cash) {
            const ok = document.querySelector('[data-current-courier]')?.dataset.currentCourier === 'pickup';
            cash.classList.toggle('disabled', !ok);
        }
    }
    form.querySelectorAll('.tab').forEach(t => t.addEventListener('click', () => show(t.dataset.type)));
    show(hidden.value || 'pickup');
})();

// Notifikasi toast: hilang otomatis (jeda saat disorot), bisa ditutup manual
(function () {
    const LIFE = 5000;
    document.querySelectorAll('.toast').forEach(function (t) {
        t.style.setProperty('--life', LIFE + 'ms');
        let left = LIFE, start = Date.now(), timer;
        const close = function () {
            t.classList.add('is-leaving');
            setTimeout(function () { t.remove(); }, 260);
        };
        const run = function () { start = Date.now(); timer = setTimeout(close, left); };
        t.addEventListener('mouseenter', function () { clearTimeout(timer); left -= Date.now() - start; });
        t.addEventListener('mouseleave', run);
        t.querySelector('.toast-close').addEventListener('click', close);
        run();
    });
})();

// Pop-up notifikasi tengah layar: tutup lewat tombol, klik di luar kotak, atau Esc
(function () {
    const pop = document.querySelector('[data-popup]');
    if (!pop) return;
    const last = document.activeElement;
    const close = function () {
        pop.classList.add('is-closing');
        setTimeout(function () { pop.remove(); if (last && last.focus) last.focus(); }, 200);
        document.removeEventListener('keydown', onKey);
    };
    const onKey = function (e) {
        if (e.key === 'Escape') close();
        if (e.key === 'Tab') { // fokus tetap di dalam pop-up
            const f = pop.querySelectorAll('a, button');
            if (e.shiftKey && document.activeElement === f[0]) { e.preventDefault(); f[f.length - 1].focus(); }
            else if (!e.shiftKey && document.activeElement === f[f.length - 1]) { e.preventDefault(); f[0].focus(); }
        }
    };
    pop.addEventListener('click', function (e) { if (e.target === pop || e.target.closest('[data-popup-close]')) close(); });
    document.addEventListener('keydown', onKey);
    const primary = pop.querySelector('.popup-actions .btn-red') || pop.querySelector('.popup-actions .btn');
    if (primary) primary.focus();
})();

// Alamat berjenjang: Provinsi -> Kota/Kabupaten -> Kecamatan (data: public/data/wilayah.json)
(function () {
    const box = document.querySelector('[data-wilayah]');
    if (!box) return;
    const prov = box.querySelector('[data-level=prov]');
    const kota = box.querySelector('[data-level=kota]');
    const kec = box.querySelector('[data-level=kec]');
    let data = null;
    const load = function () {
        if (data) return Promise.resolve(data);
        return fetch(box.dataset.wilayah)
            .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
            .then(function (d) { data = d; status.hidden = true; return d; });
    };
    // Pesan kalau daftar wilayah gagal dimuat (file hilang / koneksi putus)
    const status = document.createElement('p');
    status.className = 'field-status'; status.setAttribute('role', 'alert'); status.hidden = true;
    box.querySelector('[data-level=kota]').closest('.grid-2').after(status);
    const fail = function () {
        status.hidden = false;
        status.innerHTML = 'Daftar kota gagal dimuat. Periksa koneksi, lalu <button type="button" class="link-btn">coba lagi</button>.';
        status.querySelector('button').addEventListener('click', function () {
            status.hidden = true; prov.dispatchEvent(new Event('change'));
        });
        fill(kota, [], 'Gagal memuat daftar kota');
    };
    const fill = function (select, items, placeholder) {
        select.innerHTML = '';
        const first = document.createElement('option');
        first.value = ''; first.textContent = placeholder;
        select.appendChild(first);
        items.forEach(function (name) {
            const o = document.createElement('option');
            o.value = name; o.textContent = name;
            select.appendChild(o);
        });
        select.disabled = items.length === 0;
    };
    const findProv = function () { return data.find(function (p) { return p.n === prov.value; }); };
    prov.addEventListener('change', function () {
        fill(kota, [], 'Memuat...'); fill(kec, [], 'Pilih kota / kabupaten dulu');
        load().then(function () {
            const p = findProv();
            fill(kota, p ? p.c.map(function (c) { return c.n; }) : [], p ? 'Pilih kota / kabupaten' : 'Pilih provinsi dulu');
        }).catch(fail);
    });
    kota.addEventListener('change', function () {
        load().then(function () {
            const p = findProv();
            const c = p && p.c.find(function (c) { return c.n === kota.value; });
            fill(kec, c ? c.d : [], c ? 'Pilih kecamatan' : 'Pilih kota / kabupaten dulu');
        }).catch(fail);
    });
})();

// Jam berjalan (WIB) di form konfirmasi pembayaran
(function () {
    const el = document.querySelector('[data-clock]');
    if (!el) return;
    const fmt = new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Jakarta', weekday: 'long', day: '2-digit', month: 'long', year: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
    });
    const tick = function () {
        const p = {};
        fmt.formatToParts(new Date()).forEach(function (x) { p[x.type] = x.value; });
        el.textContent = p.weekday + ', ' + p.day + ' ' + p.month + ' ' + p.year + ', ' + p.hour + ':' + p.minute + ':' + p.second;
    };
    tick();
    setInterval(tick, 1000);
})();

// "Transfer dari Bank": tampilkan isian nama bank kalau pilih Lainnya
(function () {
    const box = document.querySelector('[data-bank]');
    if (!box) return;
    const sel = box.querySelector('select');
    const other = box.querySelector('.bank-other');
    const sync = function () {
        const on = sel.value === 'Lainnya';
        other.hidden = !on;
        other.required = on;
        if (on) other.focus();
    };
    sel.addEventListener('change', sync);
})();

// Judul muncul per kata saat di-scroll
// GSAP + ScrollTrigger + SplitText (mengikuti skill resmi gsap-core, gsap-scrolltrigger, gsap-plugins)
(function () {
    if (!window.gsap || !window.ScrollTrigger || !window.SplitText) return;
    gsap.registerPlugin(ScrollTrigger, SplitText);

    const headings = gsap.utils.toArray([
        '.hero-title', '.section-head h2', '.preorder-head h2', '.shelf h2', '.newsletter h2',
        '.page-head h1', '.detail h1', '.auth-side h2', '.auth-form h1', '.step-title .step-text'
    ].join(','));
    if (!headings.length) return;

    const mm = gsap.matchMedia();

    // Hanya jalan kalau pengguna TIDAK memilih "kurangi animasi".
    // Kalau pengaturannya berubah, matchMedia otomatis me-revert SplitText & ScrollTrigger.
    mm.add('(prefers-reduced-motion: no-preference)', function () {
        // Dibuat urut atas -> bawah sesuai urutan di halaman (urutan refresh ScrollTrigger benar)
        headings.forEach(function (el) {
            SplitText.create(el, {
                type: 'words',
                mask: 'words',              // masker per kata (overflow: clip) untuk efek muncul dari bawah
                wordsClass: 'split-word',   // masker otomatis diberi class "split-word-mask"
                ignore: '.step',            // angka langkah di keranjang tidak ikut dipecah
                aria: 'auto',               // pembaca layar tetap membaca kalimat utuh
                autoSplit: true,            // pecah ulang setelah font Archivo selesai dimuat
                onSplit: function (self) {
                    // dikembalikan supaya SplitText bisa membersihkan & menyinkronkan saat re-split
                    return gsap.from(self.words, {
                        yPercent: 110,
                        rotation: 6,
                        autoAlpha: 0,
                        duration: 0.75,
                        ease: 'power3.out',
                        stagger: 0.09,
                        scrollTrigger: { trigger: el, start: 'top 88%', once: true }
                    });
                }
            });
        });
    });

    // Gambar produk mengubah tinggi halaman setelah dimuat -> hitung ulang posisi trigger
    window.addEventListener('load', function () { ScrollTrigger.refresh(); });
})();

// Tombol "Salin nomor" rekening di form konfirmasi pembayaran
(function () {
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        const label = btn.querySelector('[data-copy-label]');
        const status = document.querySelector('[data-copy-status]');
        const original = label ? label.textContent : '';
        const fallback = function (text) {
            // untuk http biasa (bukan localhost/https) di mana navigator.clipboard tidak tersedia
            const ta = document.createElement('textarea');
            ta.value = text; ta.setAttribute('readonly', ''); ta.style.position = 'fixed'; ta.style.opacity = '0';
            document.body.appendChild(ta); ta.select();
            let ok = false;
            try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
            ta.remove();
            return ok ? Promise.resolve() : Promise.reject();
        };
        btn.addEventListener('click', function () {
            const text = btn.dataset.copy;
            const copy = navigator.clipboard && window.isSecureContext ? navigator.clipboard.writeText(text) : fallback(text);
            copy.then(function () {
                btn.classList.add('is-copied');
                if (label) label.textContent = 'Tersalin';
                if (status) status.textContent = 'Nomor rekening ' + text + ' sudah disalin.';
                setTimeout(function () { btn.classList.remove('is-copied'); if (label) label.textContent = original; }, 2000);
            }).catch(function () {
                // gagal menyalin: blok nomor supaya bisa disalin manual
                const num = document.querySelector('[data-copy-source]');
                if (num) { const r = document.createRange(); r.selectNodeContents(num); const sel = getSelection(); sel.removeAllRanges(); sel.addRange(r); }
                if (label) label.textContent = 'Tekan Ctrl+C';
                if (status) status.textContent = 'Gagal menyalin otomatis. Nomor sudah diblok, tekan Ctrl+C.';
            });
        });
    });
})();
