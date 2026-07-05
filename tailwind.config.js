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
                // Brand palette derived from the Delta Tiga Enam logo
                // white dominant · blue accent (#1C7DE0) · deep navy sections (#08182F) · cyan highlight · rare gold
                navy: {
                    DEFAULT: '#0F2F58', // headings / primary dark text
                    50: '#EEF3FB',
                    100: '#DCE7F5',
                    200: '#B9CDEA',
                    300: '#8AA8D6',
                    400: '#4F77B5',
                    500: '#2C5594',
                    600: '#173F77',
                    700: '#0F2F58',
                    800: '#0B2547',
                    900: '#0A1B3D',
                    950: '#08182F', // dark sections (hero band, CTA, footer)
                },
                // Primary blue accent (logo blue)
                sky: {
                    DEFAULT: '#1C7DE0',
                    50: '#ECF5FE',
                    100: '#D2E8FD',
                    200: '#A8D2FB',
                    300: '#72B6F7',
                    400: '#3D93F0',
                    500: '#1C7DE0',
                    600: '#1463C2',
                    700: '#114E9C',
                },
                // Cyan highlight — gradients & small accents only
                cyan: {
                    DEFAULT: '#33CFF7',
                    400: '#5BD8F9',
                    500: '#33CFF7',
                    600: '#15B6E6',
                },
                // Champagne gold — muted, premium. Used only as a tiny accent.
                gold: {
                    DEFAULT: '#C2A25A',
                    soft: '#DBC489',
                    deep: '#A6863F',
                },
                ink: '#0B1220',
                mist: '#EAF2FC',
                paper: '#F6F8FC', // off-white section background for rhythm
            },
            fontFamily: {
                // Only two typefaces site-wide: Didot (headings) + Avenir (body/UI).
                display: ['"GFS Didot"', 'Didot', 'serif'],
                sans: ['Avenir', '"Avenir Next"', 'system-ui', 'sans-serif'],
                mono: ['Avenir', '"Avenir Next"', 'system-ui', 'sans-serif'],
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
            },
            animation: {
                'aurora-drift': 'aurora-drift 18s ease-in-out infinite',
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
