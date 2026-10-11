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

    // Lamanya satu putaran dihitung dari banyaknya kartu, bukan dipatok angka
    // tetap.
    //
    // Satu putaran menggeser tepat sejumlah kartu di kolomnya (isinya digandakan
    // lalu digeser -50%). Dengan durasi tetap, makin banyak testimoni makin
    // cepat jalannya — dan itulah yang terjadi: saat tiap kolom diubah memuat
    // seluruh testimoni (dulu hanya sepertiga), isinya bertambah tiga kali lipat
    // sementara durasinya cuma naik dari 22 ke 36 detik, jadi justru hampir dua
    // kali lebih cepat daripada sebelumnya. Halaman Portofolio paling terasa
    // karena ia memuat seluruh testimoni tanpa batas, sedangkan beranda 12.
    //
    // 10 detik per kartu membuat kecepatannya sama di halaman mana pun, berapa
    // pun jumlah testimoninya. Dasar 45 detik menjaga daftar yang sangat pendek
    // tidak berputar terlalu rapat.
    $marqueeDur = fn (int $col): int => max(45, $all->count() * 10 + $col * 10);
@endphp

@if ($testimonials->isNotEmpty())
    {{-- bg-white ditulis tegas, bukan dibiarkan mewarisi latar body: seksi di
         atasnya kini bertinta biru, jadi batas keduanya harus datang dari warna
         yang memang dipilih, bukan dari kebetulan. --}}
    <section class="section bg-white">
        <div class="container">
            {{-- Tanpa label di atas judul. Judulnya sendiri sudah menyebut isinya,
                 dan label yang mengulangnya hanya menambah baris.

                 text-display-lg, token judul seksi situs ini, tanpa miring dan
                 tanpa warna kedua — sama dengan judul seksi Portofolio di halaman
                 yang sama.

                 Tetap rata tengah: kartunya berjalan di tiga kolom yang simetris,
                 dan judul rata kiri di atasnya menggantung ke satu sisi. --}}
            <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                <h2 class="text-display-lg text-navy">{{ $isId ? 'Kata mereka tentang kami' : 'What they say about us' }}</h2>
                <p class="mx-auto mt-5 max-w-xl text-pretty text-[17px] leading-[1.7] text-slate-600">
                    {{ $isId
                        ? 'Testimoni klien yang telah membuktikan hasil kerja kami.'
                        : 'Testimonials from clients who have seen our work deliver.' }}
                </p>
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
                            {{-- Selisih 10 detik antar-kolom menjaga ketiganya tidak
                                 berjalan serempak. --}}
                            <div class="marquee-col flex flex-col gap-6 {{ $ci === 1 ? '[animation-direction:reverse]' : '' }}" style="--dur: {{ $marqueeDur($ci) }}s">
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
