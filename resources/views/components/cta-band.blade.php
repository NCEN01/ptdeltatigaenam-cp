@php $id = app()->getLocale() === 'id'; @endphp
<section class="section">
    <div class="container">
        <div class="relative overflow-hidden rounded-[2rem] bg-navy-950 px-8 py-16 text-center text-white md:px-16 md:py-24">
            <div class="pointer-events-none absolute inset-0 aurora opacity-70"></div>
            <div class="relative">
                <p class="eyebrow mb-5">{{ $id ? 'Mulai langkah berikutnya' : 'Take the next step' }}</p>
                <h2 class="mx-auto max-w-3xl text-display-lg font-semibold text-balance" data-aos="fade-up">
                    {{ $id ? 'Siap meningkatkan kompetensi tim Anda?' : 'Ready to elevate your team’s competencies?' }}
                </h2>
                <div class="mt-9 flex flex-wrap justify-center gap-4" data-aos="fade-up" data-aos-delay="80">
                    <a href="{{ route('partnership.index') }}" class="btn-gold">{{ __('site.cta.partner') }}</a>
                    <a href="{{ route('contact.index') }}" class="btn-ghost-light">{{ __('site.cta.consult') }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
