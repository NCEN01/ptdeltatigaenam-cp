/**
 * Rel geser seksi "Pelatihan Terbaru Kami".
 * Dari rancangan Claude Design, dipindah ke resources/ supaya dibundel Vite
 * seperti aset lain di proyek ini. Isinya tidak diubah.
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
    function update() {
        var max = track.scrollWidth - track.clientWidth - 2;
        if (prev) prev.disabled = track.scrollLeft <= 2;
        if (next) next.disabled = track.scrollLeft >= max;
    }

    prev && prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
    next && next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
    track.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();
});
