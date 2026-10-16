/*
    Peringatan bidang wajib yang belum terisi.

    Peramban sudah punya validasi bawaan untuk [required], tapi hasilnya tidak
    bisa dipakai di situs ini: gelembung peringatannya memakai bahasa sistem
    operasi (sering berbahasa Inggris di perangkat berbahasa Indonesia),
    hilang sendiri setelah beberapa detik, dan hanya satu yang tampil
    sekaligus sehingga pengisi formulir panjang seperti Kemitraan atau
    Checkout harus menekan Kirim berkali-kali untuk menemukan sisa bidang
    yang kosong.

    Modul ini mengambil alih tampilannya saja, bukan aturannya: aturan yang
    dibaca tetap [required] dan validity bawaan peramban, dan pemeriksaan di
    peladen tidak berubah sedikit pun. Kalau JavaScript gagal dimuat,
    atribut novalidate tidak pernah terpasang sehingga validasi bawaan
    peramban kembali bekerja — bukan hilang sama sekali.

    Dipasang menyeluruh lewat satu pendengar di document, bukan per halaman:
    bidang peserta di Checkout digandakan oleh JavaScript setelah halaman
    dimuat, jadi daftar bidangnya harus dibaca saat Kirim ditekan, bukan
    saat halaman siap.
*/

const LANG = () => (document.documentElement.lang || 'id').toLowerCase().startsWith('en') ? 'en' : 'id';

const TEXT = {
    id: {
        empty: 'Bagian ini belum diisi.',
        emptyChoice: 'Pilih salah satu dulu.',
        emptyCheck: 'Bagian ini perlu dicentang.',
        badEmail: 'Alamat surel belum lengkap, contohnya nama@perusahaan.com',
        badFormat: 'Isiannya belum sesuai format yang diminta.',
        tooShort: (n) => `Isiannya terlalu pendek, minimal ${n} huruf.`,
        mismatch: 'Ulangan kata sandinya belum sama.',
        summaryOne: 'Ada 1 bagian yang belum diisi.',
        summaryMany: (n) => `Ada ${n} bagian yang belum diisi.`,
        summaryHint: 'Lengkapi bagian yang ditandai merah di bawah, lalu kirim ulang.',
    },
    en: {
        empty: 'This field is still empty.',
        emptyChoice: 'Please pick one option.',
        emptyCheck: 'This box needs to be ticked.',
        badEmail: 'That email address looks incomplete, e.g. name@company.com',
        badFormat: "This entry doesn't match the expected format.",
        tooShort: (n) => `Too short — at least ${n} characters.`,
        mismatch: "The repeated password doesn't match.",
        summaryOne: '1 field is still empty.',
        summaryMany: (n) => `${n} fields are still empty.`,
        summaryHint: 'Complete the fields marked in red below, then submit again.',
    },
};

const t = (key, arg) => {
    const entry = TEXT[LANG()][key];

    return typeof entry === 'function' ? entry(arg) : entry;
};

/** Kontrol yang ikut diperiksa: terlihat, tidak mati, dan memang wajib. */
function requiredControls(form) {
    return [...form.querySelectorAll('[required]')].filter((el) => {
        if (el.disabled || el.type === 'hidden') return false;

        // offsetParent null berarti tersembunyi — termasuk honeypot dan baris
        // peserta Checkout yang sedang disembunyikan, yang tidak boleh ikut
        // memblokir pengiriman.
        return el.offsetParent !== null || el.getClientRects().length > 0;
    });
}

/** Satu nama radio hanya dihitung sekali: yang pertama mewakili grupnya. */
function dedupeRadios(controls) {
    const seen = new Set();

    return controls.filter((el) => {
        if (el.type !== 'radio' || ! el.name) return true;
        if (seen.has(el.name)) return false;
        seen.add(el.name);

        return true;
    });
}

function isBlank(el) {
    if (el.type === 'checkbox') return ! el.checked;
    if (el.type === 'radio') {
        const group = el.form?.querySelectorAll(`[name="${CSS.escape(el.name)}"]`) ?? [];

        return ! [...group].some((r) => r.checked);
    }

    return el.value.trim() === '';
}

/** Pesan untuk satu kontrol, atau null kalau isiannya sudah benar. */
function problem(el) {
    if (isBlank(el)) {
        if (el.type === 'checkbox') return t('emptyCheck');
        if (el.type === 'radio') return t('emptyChoice');

        return t('empty');
    }

    // Ulangan kata sandi: satu-satunya aturan di sini yang membandingkan dua
    // bidang, jadi peramban tidak bisa memeriksanya sendiri. Aturan 'confirmed'
    // di peladen tetap yang menentukan; ini hanya memberitahu lebih awal
    // supaya pendaftar tidak kehilangan isian formulirnya dulu.
    if (el.name === 'password_confirmation') {
        const first = el.form?.querySelector('[name="password"]');

        if (first && first.value !== el.value) return t('mismatch');
    }

    // Terisi tapi belum lolos aturan bawaan peramban (format surel, pola,
    // panjang minimal). Pesannya ditulis ulang supaya bahasanya mengikuti
    // halaman, bukan bahasa sistem operasi pengunjung.
    if (el.checkValidity()) return null;

    const v = el.validity;

    if (v.typeMismatch && el.type === 'email') return t('badEmail');
    if (v.tooShort) return t('tooShort', el.minLength);
    if (v.patternMismatch) return t('badFormat');

    return t('badFormat');
}

/* ── Penanda per bidang ──────────────────────────────────────────────────── */

/* data-field-box dipasang <x-field>, komponen yang dipakai hampir semua bidang
   di situs ini, dan di beberapa pembungkus yang menuliskan kolomnya sendiri
   (waktu presentasi di Kemitraan, centang persetujuan di Daftar).

   Namanya bukan data-field: atribut itu sudah dipakai halaman Checkout pada
   input-nya sendiri, bernilai "name" dan "phone", untuk dibaca skrip penambah
   baris peserta di halaman itu. closest() ikut memeriksa elemen asalnya, jadi
   memakai nama yang sama membuat pesan peringatan disisipkan ke dalam <input>
   — tempat yang tidak pernah tampil di layar.

   Baris peserta Checkout tidak punya pembungkus bertanda, jadi jatuh ke
   induk terdekatnya, yaitu div yang memuat label dan input itu. */
const fieldBox = (el) => el.closest('[data-field-box]') ?? el.parentElement;

function mark(el, message) {
    const box = fieldBox(el);
    if (! box) return;

    el.classList.add('field-invalid');
    el.setAttribute('aria-invalid', 'true');

    let note = box.querySelector('[data-client-note]');

    if (! note) {
        note = document.createElement('p');
        note.dataset.clientNote = '';
        note.className = 'field-note';
        note.setAttribute('role', 'alert');
        box.appendChild(note);
    }

    note.textContent = message;
}

function unmark(el) {
    const box = fieldBox(el);

    el.classList.remove('field-invalid');
    el.removeAttribute('aria-invalid');
    box?.querySelector('[data-client-note]')?.remove();
}

/* ── Ringkasan di kepala formulir ────────────────────────────────────────── */

/* Ringkasannya disisipkan sebelum <form>, bukan sebagai anak pertamanya.
   Dua alasannya: formulir Kemitraan dan Checkout adalah kartu
   rounded-3xl overflow-hidden, jadi kotak di dalamnya akan terpotong tepi
   membulatnya; dan kotak galat dari peladen di halaman-halaman itu memang
   sudah berada tepat di atas formulirnya, sehingga peringatan dari peramban
   kini muncul di tempat yang sama. */
const existingSummary = (form) =>
    form.parentElement?.querySelector(':scope > [data-client-summary]') ?? null;

function summary(form, count) {
    let box = existingSummary(form);

    if (! box) {
        box = document.createElement('div');
        box.dataset.clientSummary = '';
        box.className = 'form-alert';
        box.setAttribute('role', 'alert');
        box.setAttribute('aria-live', 'polite');
        box.innerHTML = '<svg class="form-alert-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">'
            + '<path d="M10 1.8a8.2 8.2 0 100 16.4A8.2 8.2 0 0010 1.8zm0 3.4a1 1 0 011 1v4.4a1 1 0 01-2 0V6.2a1 1 0 011-1zm0 8.2a1.1 1.1 0 110 2.2 1.1 1.1 0 010-2.2z"/>'
            + '</svg><div><p data-client-summary-title></p><p class="form-alert-hint"></p></div>';
        form.before(box);
    }

    box.querySelector('[data-client-summary-title]').textContent =
        count === 1 ? t('summaryOne') : t('summaryMany', count);
    box.querySelector('.form-alert-hint').textContent = t('summaryHint');

    return box;
}

const clearSummary = (form) => existingSummary(form)?.remove();

/* ── Pemasangan ──────────────────────────────────────────────────────────── */

export default function initFormValidation() {
    // novalidate dipasang dari JavaScript, bukan ditulis di Blade: kalau berkas
    // ini gagal dimuat, formulirnya tetap divalidasi peramban seperti biasa.
    const claim = (form) => {
        if (form.dataset.clientValidated || form.hasAttribute('data-no-validate')) return false;
        if (! form.querySelector('[required]')) return false;

        form.dataset.clientValidated = '1';
        form.setAttribute('novalidate', '');

        return true;
    };

    document.querySelectorAll('form').forEach(claim);

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (! (form instanceof HTMLFormElement)) return;
        if (form.hasAttribute('data-no-validate')) return;

        // Formulir yang baru muncul setelah halaman dimuat (modal, Livewire)
        // diambil alih di sini, saat Kirim pertama kali ditekan.
        if (! form.dataset.clientValidated && ! claim(form)) return;

        const controls = dedupeRadios(requiredControls(form));
        const failed = [];

        controls.forEach((el) => {
            const message = problem(el);

            if (message) {
                mark(el, message);
                failed.push(el);
            } else {
                unmark(el);
            }
        });

        if (! failed.length) {
            clearSummary(form);

            return;
        }

        event.preventDefault();

        // Gulir ke ringkasannya, bukan ke bidang pertama: pengunjung perlu tahu
        // ada berapa bidang yang kurang sebelum dibawa ke salah satunya.
        summary(form, failed.length).scrollIntoView({ behavior: 'smooth', block: 'center' });

        // preventScroll supaya penyorotan fokus tidak membatalkan guliran di
        // atas — tanpa itu, peramban melompat ke bidang pertama dan ringkasan
        // yang baru muncul tidak pernah terlihat.
        failed[0].focus({ preventScroll: true });
    });

    // Penandanya hilang begitu bidangnya diperbaiki, bukan menunggu Kirim
    // ditekan lagi — peringatan yang menetap padahal sudah diisi terbaca
    // seperti galat yang tidak bisa diselesaikan.
    const recheck = (event) => {
        const el = event.target;
        if (! (el instanceof HTMLElement) || ! el.classList.contains('field-invalid')) return;
        if (problem(el)) return;

        unmark(el);

        const form = el.form;
        if (form && ! form.querySelector('.field-invalid')) clearSummary(form);
    };

    document.addEventListener('input', recheck);
    document.addEventListener('change', recheck);
}
