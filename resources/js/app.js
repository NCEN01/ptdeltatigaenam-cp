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

// Generic content carousels (testimonials, portfolio)
document.querySelectorAll('[data-carousel]').forEach((el) => {
    new Swiper(el, {
        modules: [Autoplay, Pagination],
        slidesPerView: 1.2,
        spaceBetween: 16,
        autoplay: reduceMotion ? false : { delay: 5000, disableOnInteraction: false },
        pagination: { el: el.querySelector('[data-carousel-pagination]'), clickable: true },
        breakpoints: {
            640: { slidesPerView: 2, spaceBetween: 18 },
            1024: { slidesPerView: 3, spaceBetween: 20 },
            1280: { slidesPerView: 4, spaceBetween: 20 },
        },
    });
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
        ScrollTrigger.create({
            trigger: el,
            start: 'top 90%',
            once: true,
            onEnter: () => {
                gsap.to({ val: 0 }, {
                    val: target,
                    duration: 2,
                    ease: 'power2.out',
                    onUpdate: function () {
                        el.textContent = prefix + this.targets()[0].val.toFixed(decimals) + suffix;
                    },
                });
            },
        });
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
