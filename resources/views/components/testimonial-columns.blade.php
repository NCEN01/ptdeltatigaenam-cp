@props(['testimonials'])
@php
    use Illuminate\Support\Facades\Storage;
    $isId = app()->getLocale() === 'id';

    // Distribute into 3 vertical columns
    $groups = [collect(), collect(), collect()];
    foreach ($testimonials as $i => $t) {
        $groups[$i % 3]->push($t);
    }
@endphp

@if ($testimonials->isNotEmpty())
    <section class="section">
        <div class="container">
            <div class="mx-auto max-w-xl text-center" data-aos="fade-up">
                <p class="eyebrow mb-4 inline-flex items-center"><span class="rule-gold mr-3"></span>{{ $isId ? 'Testimoni' : 'Testimonials' }}</p>
                <h2 class="text-display-lg font-normal text-navy text-balance">{{ $isId ? 'Apa kata klien kami' : 'What our clients say' }}</h2>
                <p class="mt-4 text-pretty text-navy-500">{{ $isId ? 'Cerita dampak dari mereka yang telah bekerja sama dengan kami.' : 'Impact stories from those who have worked with us.' }}</p>
            </div>

            <div class="mask-fade-y mt-14 flex max-h-[44rem] justify-center gap-6 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                @foreach ($groups as $ci => $col)
                    @if ($col->isNotEmpty())
                        <div class="{{ $ci === 1 ? 'hidden md:block' : ($ci === 2 ? 'hidden lg:block' : '') }}">
                            <div class="marquee-col flex flex-col gap-6 {{ $ci === 1 ? '[animation-direction:reverse]' : '' }}" style="--dur: {{ 22 + $ci * 4 }}s">
                                @foreach ($col->concat($col) as $t)
                                    <figure class="w-[19rem] max-w-xs rounded-3xl border border-navy-100 bg-white p-8 shadow-card">
                                        <div class="flex gap-1 text-gold">
                                            @for ($i = 0; $i < ($t->rating ?? 5); $i++)
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1.6l2.47 5.01 5.53.8-4 3.9.94 5.5L10 14.2l-4.94 2.6.94-5.5-4-3.9 5.53-.8z"/></svg>
                                            @endfor
                                        </div>
                                        <blockquote class="mt-4 text-pretty leading-relaxed text-navy-700">“{{ $t->content }}”</blockquote>
                                        <figcaption class="mt-6 flex items-center gap-3 border-t border-navy-100 pt-5">
                                            @if ($t->author_photo)
                                                <img src="{{ Storage::url($t->author_photo) }}" alt="{{ $t->author_name }}" loading="lazy" class="h-10 w-10 rounded-full object-cover">
                                            @else
                                                <span class="grid h-10 w-10 place-items-center rounded-full bg-sky-100 font-display text-sky-700">{{ \Illuminate\Support\Str::substr($t->author_name, 0, 1) }}</span>
                                            @endif
                                            <div>
                                                <p class="font-medium leading-5 tracking-tight text-navy">{{ $t->author_name }}</p>
                                                <p class="text-xs leading-5 tracking-tight text-navy-400">{{ $t->author_position }}{{ $t->author_company ? ', '.$t->author_company : '' }}</p>
                                            </div>
                                        </figcaption>
                                    </figure>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
