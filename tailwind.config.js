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
                // Brand — navy dominant, gold rare accent
                navy: {
                    DEFAULT: '#0A2A5E',
                    50: '#EEF3FB',
                    100: '#D6E2F4',
                    200: '#A9C0E6',
                    300: '#6E92CE',
                    400: '#3C63A8',
                    500: '#1E437E',
                    600: '#143462',
                    700: '#0A2A5E',
                    800: '#081F45',
                    900: '#06162F',
                    950: '#030B1A',
                },
                gold: {
                    DEFAULT: '#C9A227',
                    soft: '#E4C96A',
                    deep: '#9A7B12',
                },
                ink: '#0B1220',
                mist: '#F6F8FC',
            },
            fontFamily: {
                display: ['"Clash Display"', ...defaultTheme.fontFamily.sans],
                sans: ['"General Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },
            fontSize: {
                'display-2xl': ['clamp(3rem, 7vw, 6.5rem)', { lineHeight: '0.95', letterSpacing: '-0.03em' }],
                'display-xl': ['clamp(2.5rem, 5vw, 4.5rem)', { lineHeight: '1.0', letterSpacing: '-0.025em' }],
                'display-lg': ['clamp(2rem, 4vw, 3.25rem)', { lineHeight: '1.05', letterSpacing: '-0.02em' }],
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
                'fade-up': {
                    from: { opacity: '0', transform: 'translateY(16px)' },
                    to: { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'aurora-drift': 'aurora-drift 18s ease-in-out infinite',
                marquee: 'marquee 40s linear infinite',
                'fade-up': 'fade-up 0.7s cubic-bezier(0.16,1,0.3,1) both',
            },
        },
    },
    plugins: [typography],
};
