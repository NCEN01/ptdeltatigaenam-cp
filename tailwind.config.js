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
                // OFFICIAL LOGO PALETTE — blue #2b83df -> cyan #38e9fa; gold #dab45e/#edd68a; muted #82bdd6; derived navy darks.
                navy: {
                    DEFAULT: '#0e2237', // headings / body text on light — bluish charcoal (AA ~15:1 on white)
                    blue: '#2b83df',    // logo primary blue
                    50: '#eef5fd',
                    100: '#d7e9fb',
                    200: '#b2d4f5',
                    300: '#7fb6ee',
                    400: '#4d96e6',
                    450: '#3a86dd',
                    500: '#2b83df', // ← logo primary blue (anchor)
                    600: '#205f9f',
                    700: '#164a7d',
                    800: '#123a6b', // derived darken
                    900: '#0b2b52', // derived darken — dark sections
                    950: '#071d38', // darkest — hero band / footer base
                    muted: '#82bdd6', // captions/labels on dark backgrounds
                },
                // Light blue accent
                sky: {
                    DEFAULT: '#34d3f4',
                    50: '#e9fbff',
                    100: '#c8f5fe',
                    200: '#8fe6fb',
                    300: '#38e9fa',
                    400: '#34d3f4', // ← client light blue
                    500: '#23b7dc',
                    600: '#1d96b6',
                    700: '#1a7893',
                },
                // Light-blue highlight (gradients & small accents)
                cyan: {
                    DEFAULT: '#34d3f4',
                    400: '#38e9fa',
                    500: '#34d3f4',
                    600: '#23b7dc',
                },
                // Gold — accent only
                gold: {
                    DEFAULT: '#dab45e',
                    soft: '#edd68a',
                    deep: '#b0872f',
                },
                ink: '#0e2237', // body text — solid charcoal near-black
                mist: '#eaf3fb',
                paper: '#f2f8fd', // off-white (blue-tinted) section background for rhythm
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
                label: '0em', // small caption / eyebrow labels — normal spacing (no wide tracking)
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
