@php $id = app()->getLocale() === 'id'; @endphp
<section class="relative overflow-hidden bg-navy-950 py-20 text-center text-white md:py-28">
    <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-70"></div>
    <div class="pointer-events-none absolute inset-0 grain opacity-50"></div>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
    <div class="container relative">
        <p class="eyebrow mb-5">{{ $id ? 'Mulai langkah berikutnya' : 'Take the next step' }}</p>
        <h2 class="mx-auto max-w-3xl text-display-lg font-semibold text-balance" data-aos="fade-up">
            {{ $id ? 'Siap meningkatkan kompetensi tim Anda?' : 'Ready to elevate your team’s competencies?' }}
        </h2>
        <p class="mx-auto mt-5 max-w-xl text-pretty leading-relaxed text-navy-100" data-aos="fade-up" data-aos-delay="60">
            {{ $id ? 'Diskusikan kebutuhan pelatihan dan pengembangan SDM perusahaan Anda bersama tim kami.' : 'Discuss your company training and people development needs with our team.' }}
        </p>
        <div class="mt-9 flex flex-wrap justify-center gap-4" data-aos="fade-up" data-aos-delay="120">
            <a href="{{ route('partnership.index') }}" class="btn-blue">{{ __('site.cta.partner') }}</a>
            <a href="{{ route('contact.index') }}" class="btn-ghost-light">{{ __('site.cta.consult') }}</a>
        </div>
    </div>
</section>
