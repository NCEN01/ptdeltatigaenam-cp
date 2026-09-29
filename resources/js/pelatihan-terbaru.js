/**
 * Rel geser seksi "Pelatihan Terbaru Kami".
 * Dari rancangan Claude Design, dipindah ke resources/ supaya dibundel Vite
 * seperti aset lain di proyek ini.
 *
 * Ditambahkan sesudahnya: geser sendiri tiap beberapa detik, mengikuti
 * perilaku rel lain di situs ini (lihat initHscroll di app.js).
 */
document.querySelectorAll('.pt-section').forEach(function (sec) {
    var track = sec.querySelector('[data-pt-track]');
    var prev = sec.querySelector('[data-pt-prev]');
    var next = sec.querySelector('[data-pt-next]');
    if (!track) return;

    function step() {
        var card = track.querySelector('.pt-card');
        return card ? card.offsetWidth + parseFloat(getComputedStyle(track).columnGap || 24) : 300;
    }
    function maxScroll() {
        return track.scrollWidth - track.clientWidth - 2;
    }
    function update() {
        if (prev) prev.disabled = track.scrollLeft <= 2;
        if (next) next.disabled = track.scrollLeft >= maxScroll();
    }

    // ── Geser sendiri ──────────────────────────────────────────────────────
    // Hanya menyala bila isinya memang melebihi lebar rel: bergerak sendiri
    // padahal tidak ada yang tersembunyi hanya mengganggu pembacaan.
    var timer = null;
    var JEDA = 4000;

    function adaYangTersembunyi() {
        return track.scrollWidth > track.clientWidth + 8;
    }
    function langkah() {
        // Sudah di ujung: kembali ke awal supaya putarannya tidak pernah buntu.
        if (track.scrollLeft >= maxScroll()) {
            track.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
            track.scrollBy({ left: step(), behavior: 'smooth' });
        }
    }
    function mulaiOtomatis() {
        hentikanOtomatis();
        if (adaYangTersembunyi()) timer = setInterval(langkah, JEDA);
    }
    function hentikanOtomatis() {
        if (timer) { clearInterval(timer); timer = null; }
    }
    // Setelah pengunjung menggeser sendiri, beri jeda panjang sebelum rel
    // mengambil alih lagi — kalau tidak, ia akan menarik balik tepat saat orang
    // sedang membaca kartu yang baru saja dipilihnya.
    function tundaOtomatis() {
        hentikanOtomatis();
        setTimeout(mulaiOtomatis, 9000);
    }

    prev && prev.addEventListener('click', function () { tundaOtomatis(); track.scrollBy({ left: -step(), behavior: 'smooth' }); });
    next && next.addEventListener('click', function () { tundaOtomatis(); track.scrollBy({ left: step(), behavior: 'smooth' }); });

    sec.addEventListener('pointerenter', hentikanOtomatis);
    sec.addEventListener('pointerleave', mulaiOtomatis);
    track.addEventListener('pointerdown', tundaOtomatis);

    track.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', function () { update(); mulaiOtomatis(); });

    update();
    mulaiOtomatis();
});
