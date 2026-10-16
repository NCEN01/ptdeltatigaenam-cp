{{--
    Kerangka bersama halaman hukum: Kebijakan Privasi dan Syarat & Ketentuan.

    Keduanya satu bentuk — judul, tanggal berlaku, daftar isi yang menempel di
    sisi kiri, lalu pasal-pasal bernomor. Ditulis sekali di sini supaya tidak
    ada kemungkinan satu halaman disunting dan satunya tertinggal.

    Lebar bacanya dipatok max-w-3xl. Teks hukum dibaca menurun, bukan dipindai,
    dan baris sepanjang lebar layar membuat mata kehilangan barisnya saat
    kembali ke kiri.

    'toc' berupa larik id => judul. Daftar isinya dibangun dari situ, jadi
    nomor dan urutannya tidak bisa menyimpang dari isinya: yang muncul di
    daftar persis yang ada di badan halaman.
--}}
@props([
    'title' => '',
    'subtitle' => null,
    'updated' => null,
    'intro' => null,
    'toc' => [],
])

@php $isId = app()->getLocale() === 'id'; @endphp

<x-layout :title="$title">
    <x-page-header :title="$title" :subtitle="$subtitle" />

    <section class="section">
        <div class="container">
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-14">

                {{-- Daftar isi. Menempel saat digulir di layar lebar; di ponsel ia
                     jatuh ke atas isinya sebagai daftar biasa, bukan disembunyikan —
                     di halaman sepanjang ini, pembaca ponsel justru yang paling
                     butuh tahu ada pasal apa saja sebelum mulai menggulir. --}}
                @if (! empty($toc))
                    @php
                        // articleToc menuntut bentuk [['id' => …, 'text' => …], …],
                        // sementara $toc di sini larik id => judul. Dipetakan di sini
                        // supaya pemanggil komponen tetap menulis bentuk yang paling
                        // enak dibaca.
                        $tocItems = collect($toc)->map(fn ($label, $id) => ['id' => $id, 'text' => $label])->values()->all();
                    @endphp
                    {{-- Alpine articleToc, komponen yang sama yang dipakai halaman
                         artikel: ia mengawasi posisi tiap judul terhadap garis baca
                         140px dari atas layar, lalu menandai pasal yang sedang dibaca.
                         Dipakai ulang, bukan ditulis ulang — satu perilaku gulir untuk
                         satu situs, dan perbaikan di satu tempat berlaku di keduanya.

                         Tampilannya tidak ikut: komponen itu hanya menyediakan
                         keadaannya, dan daftar isi di sini punya rupa sendiri yang
                         menempel di sisi kiri. --}}
                    <aside class="lg:col-span-4 xl:col-span-3" x-data="articleToc(@js($tocItems))">
                        <nav class="lg:sticky lg:top-28" aria-label="{{ $isId ? 'Daftar isi' : 'Table of contents' }}">
                            <div class="flex items-center justify-between gap-4">
                                <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-slate-500">
                                    {{ $isId ? 'Daftar Isi' : 'Contents' }}
                                </p>
                                {{-- Angka kemajuan baca. Di halaman sepanjang ini,
                                     mengetahui "tinggal berapa lagi" mengubah keputusan
                                     untuk terus membaca atau berhenti. --}}
                                <p class="font-mono text-[10px] tabular-nums text-slate-400" x-text="Math.round(progress) + '%'" aria-hidden="true">0%</p>
                            </div>

                            {{-- Rel kemajuan setipis rambut di bawah labelnya. --}}
                            <div class="mt-2 h-px w-full overflow-hidden bg-navy-100" aria-hidden="true">
                                <div class="h-full bg-navy-600 transition-[width] duration-150 ease-linear" :style="`width: ${progress}%`"></div>
                            </div>

                            <ol class="mt-4 space-y-1 border-l border-navy-100">
                                @foreach ($toc as $id => $label)
                                    <li>
                                        {{-- data-no-smooth: halaman ini memakai gulir
                                             halus bawaan peramban lewat scroll-smooth di
                                             <html>, dan penangan gulir situs tidak perlu
                                             ikut campur. Sama dengan daftar isi artikel. --}}
                                        <a href="#{{ $id }}" data-no-smooth
                                           x-on:click="active = '{{ $id }}'"
                                           class="-ml-px flex gap-3 border-l-2 py-1.5 pl-4 text-[13.5px] leading-snug transition-colors duration-200"
                                           :class="active === '{{ $id }}'
                                               ? 'border-navy-600 font-medium text-navy'
                                               : 'border-transparent text-slate-600 hover:border-navy-200 hover:text-navy'"
                                           :aria-current="active === '{{ $id }}' ? 'true' : null">
                                            <span class="shrink-0 font-mono text-[11px] transition-colors duration-200"
                                                  :class="active === '{{ $id }}' ? 'text-navy-600' : 'text-slate-400'">{{ sprintf('%02d', $loop->iteration) }}</span>
                                            <span>{{ $label }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        </nav>
                    </aside>
                @endif

                <div class="{{ empty($toc) ? 'lg:col-span-12' : 'lg:col-span-8 xl:col-span-9' }}">

                    @if ($updated)
                        <p class="inline-flex items-center gap-2 rounded-full border border-navy-100 bg-navy-50 px-4 py-1.5 text-[13px] font-medium text-navy-700">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            {{ $isId ? 'Berlaku sejak' : 'Effective from' }} {{ $updated }}
                        </p>
                    @endif

                    @if ($intro)
                        {{-- Ikut dirata kanan-kiri seperti badan halamannya. text-pretty
                             dilepas: ia bekerja dengan menyetel ulang tempat patah baris
                             agar tepi kanannya enak dipandang, dan di teks yang sudah
                             dirata kanan-kiri tepi itu memang selalu lurus — jadi yang
                             tersisa hanya biayanya. --}}
                        <p class="mt-6 text-[17px] leading-[1.75] text-slate-700 [hyphens:auto] sm:text-justify">{{ $intro }}</p>
                    @endif

                    {{-- Gaya bacanya mengikuti halaman artikel: satu situs, satu cara
                         menyajikan teks panjang. Bedanya hanya penomoran pasal, yang
                         di sini dibawa oleh judulnya sendiri.

                         Butir daftar ikut dirata kanan-kiri, bukan hanya paragrafnya.
                         Dua halaman ini isinya didominasi daftar — pasal tentang hak,
                         tujuan, dan masa simpan semuanya berupa <li> — jadi merata
                         paragrafnya saja menyisakan justru bagian terbesar halaman
                         bertepi kanan bergerigi.

                         [hyphens:auto] wajib menyertainya, bukan hiasan. Teks rata
                         kanan-kiri bekerja dengan melebarkan jarak antar kata sampai
                         barisnya penuh, dan bahasa Indonesia punya banyak kata panjang
                         — "perundang-undangan", "pertanggungjawaban", "penyelenggaraan".
                         Tanpa izin memenggal, satu kata panjang yang tidak muat
                         mendorong seluruh baris sebelumnya merenggang, dan celah itu
                         menyambung ke bawah menjadi alur putih yang menarik mata keluar
                         dari barisnya. Peramban memenggal menurut lang pada <html>,
                         yang di situs ini memang sudah diisi id atau en. --}}
                    {{-- id="article-body" dibaca articleToc untuk menghitung kemajuan
                         baca: ia mengukur tinggi wadah ini terhadap jendela. Tanpa id
                         itu penandanya tetap jalan, tapi angka persennya diam di nol. --}}
                    <div id="article-body"
                         class="legal-body prose prose-lg mt-10 max-w-none [hyphens:auto]
                                prose-headings:font-display prose-headings:text-navy prose-headings:tracking-tight prose-headings:scroll-mt-28
                                prose-h2:mb-4 prose-h2:mt-12 prose-h2:text-[1.4rem] prose-h2:font-bold prose-h2:leading-snug
                                prose-h3:mb-3 prose-h3:mt-8 prose-h3:text-[1.05rem] prose-h3:font-semibold
                                prose-p:text-slate-700 prose-p:leading-[1.85] prose-p:text-left sm:prose-p:text-justify
                                prose-li:text-slate-700 prose-li:leading-[1.75] prose-li:marker:text-navy-400 prose-li:text-left sm:prose-li:text-justify
                                prose-ul:my-5 prose-ol:my-5 prose-li:my-1.5
                                prose-a:font-medium prose-a:text-sky-700 prose-a:no-underline hover:prose-a:underline
                                prose-strong:font-semibold prose-strong:text-navy
                                prose-table:text-[15px] prose-th:text-navy">
                        {{ $slot }}
                    </div>

                </div>
            </div>
        </div>
    </section>
</x-layout>
