@props(['testimonials'])
@php
    use Illuminate\Support\Facades\Storage;
    $isId = app()->getLocale() === 'id';

    // Tiap kolom memuat seluruh testimoni, hanya mulai dari titik yang berbeda.
    //
    // Kolom kedua dan ketiga baru tampil mulai md dan lg. Dulu isinya dibagi rata
    // bergilir ($i % 3), sehingga pengunjung ponsel hanya melihat sepertiga
    // testimoni — dua pertiga sisanya ada di DOM tapi display:none dan tidak
    // pernah bisa dibaca. Dengan digeser begini, setiap testimoni terbaca di
    // setiap ukuran layar, dan ketiga kolom tetap terlihat berbeda karena titik
    // mulai dan kecepatan jalannya memang tidak sama.
    $all = collect($testimonials)->values();
    $groups = [];
    foreach ([0, 1, 2] as $c) {
        $offset = intdiv($all->count() * $c, 3);
        $groups[] = $all->slice($offset)->concat($all->take($offset))->values();
    }
@endphp

@if ($testimonials->isNotEmpty())
    {{-- bg-white ditulis tegas, bukan dibiarkan mewarisi latar body: seksi di
         atasnya kini bertinta biru, jadi batas keduanya harus datang dari warna
         yang memang dipilih, bukan dari kebetulan. --}}
    <section class="section bg-white">
        <div class="container">
            {{-- Judulnya kini beraksen miring berwarna dan berbobot semibold seperti
                 judul seksi lain. Sebelumnya font-bold — satu-satunya judul seksi
                 yang lebih tebal dari yang lain tanpa alasan. --}}
            <div class="mx-auto max-w-xl text-center" data-aos="fade-up">
                <h2 class="text-display-lg text-navy">{{ $isId ? 'Testimoni Klien' : 'Client Testimonials' }}</h2>
                <p class="mt-4 text-pretty text-slate-600">{{ $isId ? 'Cerita dampak dari mereka yang telah bekerja sama dengan kami.' : 'Impact stories from those who have worked with us.' }}</p>
            </div>

            {{-- Di bawah md hanya satu kolom yang tampil, dan sebelumnya kolom itu
                 berisi sepertiga testimoni karena pembagiannya bergilir ($i % 3) —
                 dua pertiga sisanya ada di DOM tapi display:none, jadi pengunjung
                 ponsel tidak pernah bisa membacanya sama sekali. Sekarang jumlah
                 kolomnya yang menentukan pembagian: satu kolom di ponsel berisi
                 seluruh testimoni, dua di md, tiga di lg. --}}
            <div class="mask-fade-y mt-14 flex max-h-[44rem] justify-center gap-6 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                @foreach ($groups as $ci => $col)
                    @if ($col->isNotEmpty())
                        <div class="min-w-0 {{ $ci === 1 ? 'hidden md:block' : ($ci === 2 ? 'hidden lg:block' : '') }}">
                            {{-- 36/44/52 detik, dari 22/26/30 sebelumnya. Kolomnya
                                 memuat kutipan beberapa baris, dan pada kecepatan
                                 lama baris paling bawah sudah lewat sebelum orang
                                 selesai membacanya. Selisih antar-kolom dilebarkan
                                 jadi 8 detik supaya ketiganya tetap terlihat tidak
                                 serempak meski semuanya melambat. --}}
                            <div class="marquee-col flex flex-col gap-6 {{ $ci === 1 ? '[animation-direction:reverse]' : '' }}" style="--dur: {{ 36 + $ci * 8 }}s">
                                @foreach ($col->concat($col) as $t)
                                    {{-- w-[17rem] di lg: tiga kartu 19rem + dua jarak 1,5rem
                                         berjumlah tepat 960px, yaitu lebar isi container di
                                         1024px — nol kelonggaran, sehingga bayangan kartu
                                         terluar terpotong rata oleh overflow-hidden. --}}
                                    <figure class="w-[17rem] max-w-xs rounded-3xl border border-navy-100 bg-white p-8 shadow-card sm:w-[19rem]">
                                        {{-- gold-deep, bukan gold: bintangnya membawa nilai
                                             penilaian, jadi ia grafik bermakna yang harus
                                             mencapai 3:1. Emas #dab45e di atas putih hanya
                                             1,97:1; gold-deep 3,31:1. Angkanya juga ditulis
                                             untuk pembaca layar, yang tidak bisa menghitung
                                             jumlah bentuk. --}}
                                        <div class="flex gap-1 text-gold-deep">
                                            @for ($i = 0; $i < ($t->rating ?? 5); $i++)
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 1.6l2.47 5.01 5.53.8-4 3.9.94 5.5L10 14.2l-4.94 2.6.94-5.5-4-3.9 5.53-.8z"/></svg>
                                            @endfor
                                            <span class="sr-only">{{ $isId ? 'Penilaian' : 'Rating' }} {{ $t->rating ?? 5 }}/5</span>
                                        </div>
                                        <blockquote class="mt-4 text-pretty leading-relaxed text-slate-800">“{{ $t->content }}”</blockquote>
                                        <figcaption class="mt-6 flex items-center gap-3 border-t border-navy-100 pt-5">
                                            @if ($t->author_photo)
                                                <img src="{{ Storage::url($t->author_photo) }}" alt="{{ $t->author_name }}" loading="lazy" class="h-10 w-10 rounded-full object-cover">
                                            @else
                                                {{-- navy di atas navy-50, bukan sky-700 di atas
                                                     sky-100: pasangan yang kedua hanya 4,0:1,
                                                     di bawah ambang 4,5:1 untuk teks. --}}
                                                <span class="grid h-10 w-10 place-items-center rounded-full bg-navy-50 font-display font-semibold text-navy">{{ \Illuminate\Support\Str::substr($t->author_name, 0, 1) }}</span>
                                            @endif
                                            <div>
                                                <p class="font-medium leading-5 tracking-tight text-navy">{{ $t->author_name }}</p>
                                                <p class="text-xs leading-5 tracking-tight text-slate-500">{{ $t->author_position }}{{ $t->author_company ? ', '.$t->author_company : '' }}</p>
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
