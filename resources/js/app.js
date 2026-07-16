import './bootstrap';

import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Swiper from 'swiper';
import { Autoplay, EffectFade, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/pagination';

window.Alpine = Alpine;
// Shared UI state (e.g. mobile nav open) so decoupled components — like the floating
// WhatsApp / scroll-to-top helpers — can react to it.
Alpine.store('ui', { navOpen: false });
Alpine.start();

gsap.registerPlugin(ScrollTrigger);

// Animations are kept ON regardless of the OS "reduce motion" setting (marketing site).
// To respect the OS preference again, use:
//   const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const reduceMotion = false;

AOS.init({
    once: true,
    duration: reduceMotion ? 0 : 700,
    easing: 'ease-out-cubic',
    offset: 80,
    disable: reduceMotion,
});

// Recalculate scroll positions after assets settle (hero carousel, fan, images, fonts
// change layout heights AFTER init — otherwise lower sections like Testimonials → CTA
// trigger at the wrong scroll point and appear delayed).
const refreshScroll = () => { AOS.refresh(); ScrollTrigger.refresh(); };
window.addEventListener('load', refreshScroll);
setTimeout(refreshScroll, 600);
if (document.fonts && document.fonts.ready) document.fonts.ready.then(refreshScroll);

// Initialize the interactive carousels up-front and in isolation. `initCoverflow` and
// `initHscroll` are hoisted function declarations (defined lower in this file), so calling
// them here — before any other enhancement module runs — guarantees they work even if a
// later module throws. Without this, a single downstream error left the coverflow cards
// stacked and the Blog/Agenda arrows + auto-scroll dead.
try { initCoverflow(); } catch (e) { console.error('[coverflow]', e); }
try { initHscroll(); } catch (e) { console.error('[hscroll]', e); }

// Cursor spotlight glow on cards — delegated; positions the radial glow under the cursor.
document.addEventListener('pointermove', (e) => {
    const el = e.target.closest('[data-spotlight]');
    if (!el) return;
    const r = el.getBoundingClientRect();
    el.style.setProperty('--sx', `${((e.clientX - r.left) / r.width) * 100}%`);
    el.style.setProperty('--sy', `${((e.clientY - r.top) / r.height) * 100}%`);
}, { passive: true });

// Button press ripple — delegated, so it also covers buttons added later. The soft
// scale-down on :active is handled in CSS. Disabled under reduced-motion.
if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.addEventListener('pointerdown', (e) => {
        const btn = e.target.closest('.btn, [data-ripple]');
        if (!btn || btn.hasAttribute('disabled')) return;
        const rect = btn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const span = document.createElement('span');
        span.className = 'btn-ripple';
        span.style.width = span.style.height = `${size}px`;
        span.style.left = `${e.clientX - rect.left - size / 2}px`;
        span.style.top = `${e.clientY - rect.top - size / 2}px`;
        btn.appendChild(span);
        span.addEventListener('animationend', () => span.remove());
        setTimeout(() => span.remove(), 800);
    }, { passive: true });
}

// Hero category rotator
const heroEl = document.querySelector('[data-hero-swiper]');
if (heroEl) {
    new Swiper(heroEl, {
        modules: [Autoplay, EffectFade, Pagination, Navigation],
        effect: 'fade',
        fadeEffect: { crossFade: true },
        loop: true,
        speed: 800,
        autoplay: reduceMotion ? false : { delay: 4500, disableOnInteraction: false },
        pagination: { el: heroEl.querySelector('[data-hero-pagination]'), clickable: true },
        navigation: {
            nextEl: heroEl.querySelector('[data-hero-next]'),
            prevEl: heroEl.querySelector('[data-hero-prev]'),
        },
    });
}

// Hero promo slider — 3 categories sliding sideways
const heroPromo = document.querySelector('[data-hero-promo]');
if (heroPromo) {
    new Swiper(heroPromo, {
        modules: [Autoplay, Pagination],
        slidesPerView: 1,
        loop: true,
        speed: 700,
        autoHeight: true,
        autoplay: reduceMotion ? false : { delay: 3800, disableOnInteraction: false },
        pagination: { el: document.querySelector('[data-hero-promo-pagination]'), clickable: true },
    });
}

// Hero image carousel — full-width horizontal slider
const heroCar = document.querySelector('[data-hero-carousel]');
if (heroCar) {
    new Swiper(heroCar, {
        modules: [Autoplay, Pagination, Navigation],
        slidesPerView: 1,
        loop: true,
        speed: 800,
        autoplay: reduceMotion ? false : { delay: 4500, disableOnInteraction: false, pauseOnMouseEnter: true },
        pagination: { el: heroCar.querySelector('[data-hero-carousel-pagination]'), clickable: true },
        navigation: { prevEl: heroCar.querySelector('[data-hero-prev]'), nextEl: heroCar.querySelector('[data-hero-next]') },
    });
}

// Hero rotating promo phrases (cycles 3 categories)
document.querySelectorAll('[data-rotator]').forEach((el) => {
    const items = [...el.querySelectorAll('[data-rotate-item]')];
    if (items.length < 2) return;
    items.forEach((it, i) => it.classList.toggle('is-active', i === 0));
    if (reduceMotion) return;
    let i = 0;
    setInterval(() => {
        items[i].classList.remove('is-active');
        i = (i + 1) % items.length;
        items[i].classList.add('is-active');
    }, 3200);
});

// Card-fan carousel — opens on scroll-down, closes on scroll-up (GSAP ScrollTrigger scrub)
const fanMult = (w) => (w < 480 ? 0.32 : w < 640 ? 0.42 : w < 768 ? 0.55 : w < 1024 ? 0.78 : 1);
function fanSpread(total, i) {
    const center = (total - 1) / 2;
    const dist = total > 1 ? (i - center) / center : 0; // -1 .. 1
    const a = Math.abs(dist);
    return { rot: dist * 21, scale: 1 - 0.18 * a * a, x: dist * 30, y: a * a * 7.3, z: 100 - Math.round(a * 100) };
}
document.querySelectorAll('[data-fan]').forEach((root) => {
    const cards = [...root.querySelectorAll('.fan-card')];
    const total = cards.length;
    if (!total) return;
    const wrap = root.closest('[data-fan-wrap]') || root.parentElement;
    const mult = () => fanMult(window.innerWidth);
    const closedRot = (i) => (i - (total - 1) / 2) * 2; // tiny tilt so the stack reads as a deck

    // Reduced motion → show the fan open, no scroll animation
    if (reduceMotion) {
        cards.forEach((card, i) => {
            const c = fanSpread(total, i);
            gsap.set(card, { x: `${c.x * mult()}rem`, y: `${c.y}rem`, rotation: c.rot, scale: c.scale, zIndex: c.z });
        });
        return;
    }

    // Scroll-linked open/close: progress 0 = stacked (closed), progress 1 = fanned (open)
    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: wrap,
            start: 'top 82%',
            end: 'top 32%',
            scrub: 0.6,
            invalidateOnRefresh: true,
        },
    });
    cards.forEach((card, i) => {
        const c = fanSpread(total, i);
        tl.fromTo(card,
            { x: 0, y: 0, rotation: closedRot(i), scale: 0.9, zIndex: total - i },
            { x: () => `${c.x * mult()}rem`, y: `${c.y}rem`, rotation: c.rot, scale: c.scale, zIndex: c.z, ease: 'power2.out' },
            i * 0.04
        );
    });
});

// Interactive 3D tilt cards (pointer-driven). Sets CSS vars consumed by .card-3d
if (!reduceMotion) {
    document.querySelectorAll('[data-tilt]').forEach((el) => {
        const MAX = 7; // max degrees of rotation
        const onMove = (e) => {
            const r = el.getBoundingClientRect();
            const px = (e.clientX - r.left) / r.width;
            const py = (e.clientY - r.top) / r.height;
            el.style.setProperty('--tilt-x', `${(px - 0.5) * MAX * 2}deg`);
            el.style.setProperty('--tilt-y', `${(0.5 - py) * MAX * 2}deg`);
            el.style.setProperty('--mx', `${px * 100}%`);
            el.style.setProperty('--my', `${py * 100}%`);
        };
        const reset = () => {
            el.style.setProperty('--tilt-x', '0deg');
            el.style.setProperty('--tilt-y', '0deg');
        };
        el.addEventListener('pointermove', onMove);
        el.addEventListener('pointerleave', reset);
    });
}

// Generic content carousels (testimonials, portfolio, agenda)
document.querySelectorAll('[data-carousel]').forEach((el) => {
    try {
        const prevEl = el.parentElement.querySelector('[data-carousel-prev]');
        const nextEl = el.parentElement.querySelector('[data-carousel-next]');
        const hasNav = !!(prevEl && nextEl);
        const config = {
            // Only load the Navigation module when arrow buttons exist — passing
            // `navigation: undefined` while the module is active crashes Swiper
            // (it reads params.navigation.enabled on an undefined object).
            modules: hasNav ? [Autoplay, Navigation, Pagination] : [Autoplay, Pagination],
            slidesPerView: 1.2,
            spaceBetween: 16,
            autoplay: reduceMotion ? false : { delay: 5000, disableOnInteraction: false, pauseOnMouseEnter: true },
            pagination: { el: el.parentElement.querySelector('[data-carousel-pagination]'), clickable: true },
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 18 },
                1024: { slidesPerView: 3, spaceBetween: 20 },
                1280: { slidesPerView: 4, spaceBetween: 20 },
            },
        };
        if (hasNav) config.navigation = { prevEl, nextEl };
        new Swiper(el, config);
    } catch (e) { console.error('[carousel]', e); }
});

// ═══════════════════════════════════════════
//  SCROLL-TRIGGERED TEXT REVEAL (word-by-word)
// ═══════════════════════════════════════════
if (!reduceMotion) {
    document.querySelectorAll('[data-text-reveal]').forEach((el) => {
        const text = el.textContent.trim();
        const words = text.split(' ');
        el.innerHTML = words.map((w, i) =>
            `<span class="text-reveal-word" style="transition-delay:${i * 0.04}s">${w} </span>`
        ).join('');
        el.style.visibility = 'visible';

        ScrollTrigger.create({
            trigger: el,
            start: 'top 85%',
            once: true,
            onEnter: () => {
                el.querySelectorAll('.text-reveal-word').forEach(w => w.classList.add('is-visible'));
            },
        });
    });
}

// ═══════════════════════════════════════════
//  PARALLAX SCROLL EFFECTS
// ═══════════════════════════════════════════
if (!reduceMotion) {
    document.querySelectorAll('[data-parallax]').forEach((el) => {
        const speed = parseFloat(el.dataset.parallax) || 0.3;
        gsap.to(el, {
            y: () => el.offsetHeight * speed,
            ease: 'none',
            scrollTrigger: {
                trigger: el.closest('section') || el.parentElement,
                start: 'top bottom',
                end: 'bottom top',
                scrub: true,
            },
        });
    });
}

// ═══════════════════════════════════════════
//  MAGNETIC HOVER ON BUTTONS & INTERACTIVE ELEMENTS
// ═══════════════════════════════════════════
if (!reduceMotion) {
    document.querySelectorAll('[data-magnetic]').forEach((el) => {
        const strength = parseFloat(el.dataset.magnetic) || 20;
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            const x = (e.clientX - r.left - r.width / 2) / (r.width / 2);
            const y = (e.clientY - r.top - r.height / 2) / (r.height / 2);
            gsap.to(el, {
                x: x * strength * 0.5,
                y: y * strength * 0.5,
                duration: 0.5,
                ease: 'power2.out',
                overwrite: 'auto',
            });
        });
        el.addEventListener('pointerleave', () => {
            gsap.to(el, { x: 0, y: 0, duration: 0.6, ease: 'elastic.out(1,0.4)', overwrite: 'auto' });
        });
    });
}

// ═══════════════════════════════════════════
//  COUNTER ANIMATION (stat numbers)
// ═══════════════════════════════════════════
if (!reduceMotion) {
    document.querySelectorAll('[data-counter]').forEach((el) => {
        const target = parseFloat(el.dataset.counter);
        const suffix = el.dataset.counterSuffix || '';
        const prefix = el.dataset.counterPrefix || '';
        const decimals = parseInt(el.dataset.counterDecimals) || 0;

        const animate = () => {
            gsap.to({ val: 0 }, {
                val: target,
                duration: 2,
                ease: 'power2.out',
                onUpdate: function () {
                    el.textContent = prefix + this.targets()[0].val.toFixed(decimals) + suffix;
                },
            });
        };

        // If the element is already visible (e.g. hero section), animate immediately.
        const rect = el.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight && rect.bottom > 0;

        if (isVisible) {
            // Small delay so the page layout has settled
            setTimeout(animate, 300);
        } else {
            ScrollTrigger.create({
                trigger: el,
                start: 'top 90%',
                once: true,
                onEnter: () => animate(),
            });
        }
    });
}

// ═══════════════════════════════════════════
//  STAGGER REVEAL for list children on scroll
// ═══════════════════════════════════════════
if (!reduceMotion) {
    document.querySelectorAll('[data-stagger]').forEach((el) => {
        ScrollTrigger.create({
            trigger: el,
            start: 'top 88%',
            once: true,
            onEnter: () => el.classList.add('is-visible'),
        });
    });
}

// ═══════════════════════════════════════════
//  SMOOTH INTERNAL LINK SCROLLING (offset header)
// ═══════════════════════════════════════════
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            const offset = parseInt(target.dataset.scrollOffset) || 100;
            const top = target.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top, behavior: reduceMotion ? 'auto' : 'smooth' });
        }
    });
});

// ═══════════════════════════════════════════
//  CATEGORY COVERFLOW (drag · snap · 3D)
// ═══════════════════════════════════════════
function initCoverflow() {
    document.querySelectorAll('[data-coverflow]').forEach((root) => {
    const stage = root.querySelector('[data-cf-stage]');
    const cards = Array.from(root.querySelectorAll('[data-cf-card]'));
    const dotsWrap = root.querySelector('[data-cf-dots]');
    const prevBtn = root.querySelector('[data-cf-prev]');
    const nextBtn = root.querySelector('[data-cf-next]');
    const n = cards.length;
    if (!stage || n === 0) return;

    let active = Math.floor(n / 2);
    let pos = active;
    let dragging = false;
    let startX = 0;
    let startPos = 0;
    let moved = false;
    let autoTimer = null;
    let dir = 1;

    const dots = [];
    if (dotsWrap) {
        cards.forEach((_, i) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'cf-dot';
            b.setAttribute('aria-label', 'Kategori ' + (i + 1));
            b.addEventListener('click', () => { stopAuto(); goTo(i); });
            dotsWrap.appendChild(b);
            dots.push(b);
        });
    }

    const spacing = () => Math.max(150, (cards[0].offsetWidth || 300) * 0.64);

    function render(instant) {
        const sp = spacing();
        cards.forEach((card, i) => {
            const off = i - pos;
            const abs = Math.abs(off);
            const rot = Math.max(-40, Math.min(40, -off * 40));
            const tz = -abs * 200;
            const tx = off * sp;
            const scale = off === 0 ? 1.06 : Math.max(0.78, 1 - abs * 0.09);
            const opacity = abs > 2.7 ? 0 : Math.max(0.2, 1 - abs * 0.3);
            card.style.transition = instant
                ? 'none'
                : 'transform .55s cubic-bezier(.22,.7,.25,1), opacity .55s ease';
            card.style.transform =
                'translate(-50%,-50%) translateX(' + tx + 'px) translateZ(' + tz + 'px) rotateY(' + rot + 'deg) scale(' + scale + ')';
            card.style.opacity = opacity;
            card.style.zIndex = String(200 - Math.round(abs * 10));
            card.classList.toggle('is-active', Math.round(pos) === i);
        });
        const ai = Math.max(0, Math.min(n - 1, Math.round(pos)));
        dots.forEach((d, i) => d.classList.toggle('is-on', i === ai));
    }

    function goTo(i, instant) {
        active = Math.max(0, Math.min(n - 1, i));
        pos = active;
        render(instant);
    }

    function onDown(e) {
        dragging = true;
        moved = false;
        startX = e.clientX;
        startPos = pos;
        stage.classList.add('is-grabbing');
        stopAuto();
        // NOTE: no setPointerCapture — it would hijack the card's click event and
        // block navigation. Window-level pointermove/up already track the drag.
    }
    function onMove(e) {
        if (!dragging) return;
        const dx = e.clientX - startX;
        if (Math.abs(dx) > 4) moved = true;
        pos = Math.max(-0.5, Math.min(n - 0.5, startPos - dx / spacing()));
        render(true);
    }
    function onUp() {
        if (!dragging) return;
        dragging = false;
        stage.classList.remove('is-grabbing');
        goTo(Math.round(pos));
        startAuto();
    }

    stage.addEventListener('pointerdown', onDown);
    window.addEventListener('pointermove', onMove);
    window.addEventListener('pointerup', onUp);
    window.addEventListener('pointercancel', onUp);

    cards.forEach((card) => {
        card.addEventListener('click', (e) => {
            // A genuine click navigates to the category; a drag does not.
            if (moved) e.preventDefault();
        });
    });

    if (prevBtn) prevBtn.addEventListener('click', () => { stopAuto(); goTo(active - 1); });
    if (nextBtn) nextBtn.addEventListener('click', () => { stopAuto(); goTo(active + 1); });

    function tick() {
        let next = active + dir;
        if (next > n - 1) { dir = -1; next = active - 1; }
        else if (next < 0) { dir = 1; next = active + 1; }
        goTo(next);
    }
    function startAuto() { stopAuto(); if (n > 1 && !reduceMotion) autoTimer = setInterval(tick, 4000); }
    function stopAuto() { if (autoTimer) { clearInterval(autoTimer); autoTimer = null; } }
    root.addEventListener('pointerenter', stopAuto);
    root.addEventListener('pointerleave', () => { if (!dragging) startAuto(); });

    let rt;
    window.addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(() => render(true), 120); });

    goTo(active, true);
    startAuto();
    // Re-measure once the page (fonts/images) has fully settled.
    window.addEventListener('load', () => { try { render(true); } catch (_) {} });
    });
}

// ═══════════════════════════════════════════
//  HORIZONTAL SCROLL CAROUSEL (prev / next · drag · auto)
// ═══════════════════════════════════════════
function initHscroll() {
    document.querySelectorAll('[data-hscroll]').forEach((root) => {
    const track = root.querySelector('[data-hscroll-track]');
    if (!track) return;
    // Scroll by a full "page" (however many whole cards are visible) so each nudge
    // reveals the next set — falls back to ~90% of the viewport when empty.
    const amount = () => {
        const first = track.firstElementChild;
        if (first) {
            const gap = parseFloat(getComputedStyle(track).columnGap || '0') || 0;
            const step = first.getBoundingClientRect().width + gap;
            const perView = Math.max(1, Math.round(track.clientWidth / step));
            return Math.round(step * perView);
        }
        return Math.round(track.clientWidth * 0.9);
    };
    const prev = root.querySelector('[data-hscroll-prev]');
    const next = root.querySelector('[data-hscroll-next]');
    if (prev) prev.addEventListener('click', () => { pauseAuto(); track.scrollBy({ left: -amount(), behavior: 'smooth' }); });
    if (next) next.addEventListener('click', () => { pauseAuto(); track.scrollBy({ left: amount(), behavior: 'smooth' }); });

    // Drag / swipe to scroll.
    let down = false, moved = false, startX = 0, startLeft = 0;
    track.addEventListener('pointerdown', (e) => {
        down = true; moved = false; startX = e.clientX; startLeft = track.scrollLeft;
    });
    window.addEventListener('pointermove', (e) => {
        if (!down) return;
        const dx = e.clientX - startX;
        if (Math.abs(dx) > 4) moved = true;
        track.scrollLeft = startLeft - dx;
    });
    const up = () => { down = false; };
    window.addEventListener('pointerup', up);
    window.addEventListener('pointercancel', up);
    // Swallow the click that ends a drag so cards don't navigate mid-swipe.
    track.addEventListener('click', (e) => { if (moved) { e.preventDefault(); e.stopPropagation(); } }, true);

    // Auto-advance when opted in (data-hscroll-auto) and there is overflow.
    let timer = null;
    const canAuto = root.hasAttribute('data-hscroll-auto') && !reduceMotion;
    const atEnd = () => track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
    const tick = () => { atEnd() ? track.scrollTo({ left: 0, behavior: 'smooth' }) : track.scrollBy({ left: amount(), behavior: 'smooth' }); };
    function startAuto() { stopAuto(); if (canAuto && track.scrollWidth > track.clientWidth + 8) timer = setInterval(tick, 3800); }
    function stopAuto() { if (timer) { clearInterval(timer); timer = null; } }
    function pauseAuto() { stopAuto(); setTimeout(startAuto, 7000); }
    root.addEventListener('pointerenter', stopAuto);
    root.addEventListener('pointerleave', startAuto);
    startAuto();
    });
}
