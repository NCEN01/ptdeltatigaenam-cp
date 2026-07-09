import defaultTheme from 'tailwindcss/defaultTheme';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        container: {
            center: true,
            padding: { DEFAULT: '1.25rem', lg: '2rem', xl: '2.5rem' },
            screens: { '2xl': '1280px' },
        },
        extend: {
            colors: {
                // Brand palette (client-specified): dark blue #1565c0 · light blue #48cae4 · gold #edae49
                navy: {
                    DEFAULT: '#12529b', // headings / primary dark text (readable dark blue)
                    50: '#eaf1fb',
                    100: '#cbddf6',
                    200: '#a3c4ee',
                    300: '#72a4e4',
                    400: '#3f82d8',
                    450: '#2670cb',
                    500: '#1565c0', // ← client dark blue (anchor)
                    600: '#1258a8',
                    700: '#0f4890',
                    800: '#0d3c78',
                    900: '#0b3161',
                    950: '#0a2b52', // darkest — dark sections (hero band, CTA, footer)
                },
                // Light blue accent
                sky: {
                    DEFAULT: '#48cae4',
                    50: '#ebfafd',
                    100: '#c9f2fb',
                    200: '#9ae7f6',
                    300: '#69d8ed',
                    400: '#48cae4', // ← client light blue
                    500: '#22b0d0',
                    600: '#1592b4',
                    700: '#127491',
                },
                // Light-blue highlight (gradients & small accents)
                cyan: {
                    DEFAULT: '#48cae4',
                    400: '#69d8ed',
                    500: '#48cae4',
                    600: '#22b0d0',
                },
                // Gold — accent only
                gold: {
                    DEFAULT: '#edae49',
                    soft: '#f6c877',
                    deep: '#c68a2b',
                },
                ink: '#0b2545',
                mist: '#e8f2fb',
                paper: '#f3f8fd', // off-white (blue-tinted) section background for rhythm
            },
            fontFamily: {
                // Display / labels / UI chrome — Plus Jakarta Sans (the "main" voice).
                display: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                mono: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                // Body & description prose — Figtree.
                sans: ['Figtree', 'system-ui', 'sans-serif'],
            },
            fontSize: {
                // Serif display — line-height & tracking tuned for Young Serif's chunky forms.
                'display-2xl': ['clamp(2.6rem, 6vw, 5rem)', { lineHeight: '1.03', letterSpacing: '-0.01em' }],
                'display-xl': ['clamp(2.15rem, 4.6vw, 3.6rem)', { lineHeight: '1.06', letterSpacing: '-0.008em' }],
                'display-lg': ['clamp(1.85rem, 3.6vw, 2.75rem)', { lineHeight: '1.1', letterSpacing: '-0.005em' }],
            },
            letterSpacing: {
                label: '0.22em',
            },
            boxShadow: {
                card: '0 1px 2px rgba(10,42,94,0.04), 0 8px 24px -12px rgba(10,42,94,0.18)',
                lift: '0 2px 4px rgba(10,42,94,0.05), 0 24px 48px -20px rgba(10,42,94,0.28)',
                gold: '0 8px 30px -12px rgba(201,162,39,0.45)',
            },
            transitionTimingFunction: {
                'out-soft': 'cubic-bezier(0.16, 1, 0.3, 1)',
            },
            keyframes: {
                'aurora-drift': {
                    '0%, 100%': { transform: 'translate3d(-4%, -2%, 0) scale(1.05)' },
                    '50%': { transform: 'translate3d(4%, 3%, 0) scale(1.15)' },
                },
                marquee: {
                    from: { transform: 'translateX(0)' },
                    to: { transform: 'translateX(-50%)' },
                },
                'marquee-reverse': {
                    from: { transform: 'translateX(-50%)' },
                    to: { transform: 'translateX(0)' },
                },
                'marquee-y': {
                    from: { transform: 'translateY(0)' },
                    to: { transform: 'translateY(-50%)' },
                },
                'fade-up': {
                    from: { opacity: '0', transform: 'translateY(16px)' },
                    to: { opacity: '1', transform: 'translateY(0)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-28px)' },
                },
                'float-slow': {
                    '0%, 100%': { transform: 'translate(0, 0)' },
                    '50%': { transform: 'translate(30px, -24px)' },
                },
                'spin-slow': {
                    to: { transform: 'rotate(360deg)' },
                },
                'pulse-glow': {
                    '0%, 100%': { opacity: '0.4' },
                    '50%': { opacity: '0.85' },
                },
                'gradient-pan': {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
            },
            // Tighter, more decisive radius scale (tokens). Elements with no radius
            // stay square — only elements already using rounded-* pick these up.
            borderRadius: {
                sm: '4px',
                DEFAULT: '5px',
                md: '6px',
                lg: '8px',
                xl: '8px',
                '2xl': '10px',
                '3xl': '12px',
            },
            animation: {
                'aurora-drift': 'aurora-drift 18s ease-in-out infinite',
                'gradient-pan': 'gradient-pan 22s ease-in-out infinite',
                marquee: 'marquee 40s linear infinite',
                'marquee-slow': 'marquee 55s linear infinite',
                'marquee-reverse': 'marquee-reverse 48s linear infinite',
                'marquee-y': 'marquee-y 24s linear infinite',
                'fade-up': 'fade-up 0.7s cubic-bezier(0.16,1,0.3,1) both',
                float: 'float 8s ease-in-out infinite',
                'float-slow': 'float-slow 14s ease-in-out infinite',
                'spin-slow': 'spin-slow 40s linear infinite',
                'pulse-glow': 'pulse-glow 6s ease-in-out infinite',
            },
        },
    },
    plugins: [typography],
};
