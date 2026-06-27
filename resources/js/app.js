import './bootstrap';

import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';
import Swiper from 'swiper';
import { Autoplay, EffectFade, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/pagination';

window.Alpine = Alpine;
Alpine.start();

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

AOS.init({
    once: true,
    duration: reduceMotion ? 0 : 700,
    easing: 'ease-out-cubic',
    offset: 80,
    disable: reduceMotion,
});

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

// Generic content carousels (testimonials, portfolio)
document.querySelectorAll('[data-carousel]').forEach((el) => {
    new Swiper(el, {
        modules: [Autoplay, Pagination],
        slidesPerView: 1.1,
        spaceBetween: 20,
        autoplay: reduceMotion ? false : { delay: 5000, disableOnInteraction: false },
        pagination: { el: el.querySelector('[data-carousel-pagination]'), clickable: true },
        breakpoints: {
            640: { slidesPerView: 2, spaceBetween: 24 },
            1024: { slidesPerView: 3, spaceBetween: 28 },
        },
    });
});
