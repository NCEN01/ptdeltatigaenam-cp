@php
    use App\Models\Setting;

    $id = app()->getLocale() === 'id';
    $email = 'info@deltatigaenam.com';
    // config/company.php, sumber yang sama dengan pita ajakan media sosial di
    // beranda. Ditulis di sini juga, keduanya menyimpang begitu salah satunya
    // diperbarui.
    $facebook = config('company.social.facebook');
    $instagram = config('company.social.instagram');
    $linkedin = Setting::get('linkedin_url', 'https://linkedin.com/company/deltatigaenam');
    $year = now()->year;

    // Alamat kantor dibaca dari config/company.php — sumber yang sama dengan
    // halaman kontak, jadi keduanya tidak bisa berbeda.
    $offices = \App\Support\Company::offices();

    // Footer "Layanan" list — mirror the real, active service categories (top 6 by order).
    $footerServices = \App\Models\ServiceCategory::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->take(6)
        ->get(['name', 'slug']);

    $nav = [
        'about' => __('site.nav.about'),
        'services.index' => __('site.nav.services'),
        'certificates.index' => __('site.nav.certificates'),
        'portfolio.index' => __('site.nav.portfolio'),
        'blog.index' => __('site.nav.blog'),
        // Agenda disembunyikan lewat config/company.php, sama seperti di menu atas.
        ...(config('company.pages.agenda', true)
            ? ['agenda.index' => __('site.nav.agenda')]
            : []),
        'partnership.index' => __('site.nav.partnership'),
        'contact.index' => __('site.nav.contact'),
    ];

    // Kartu ajakan yang dulu melayang di atas footer sudah dilepas, beserta
    // seluruh tabel salinannya per halaman. Halaman-halaman itu sudah punya
    // ajakan sendiri di dalam isinya; yang di sini hanya mengulang, dan
    // kartunya yang menimpa tepi footer memaksa footer memakai -mt-24 dengan
    // pt-32 sebagai penyeimbang — dua angka yang harus dijaga bersamaan.
@endphp

{{-- Solid blue-gradient footer: white text, gold sub-headings, gold hover --}}
<footer class="relative overflow-hidden text-white"
        style="background: radial-gradient(85% 60% at 88% 0%, rgba(43,131,223,0.22) 0%, transparent 60%), linear-gradient(180deg, #205f9f 0%, #123a6b 38%, #071d38 74%, #071d38 100%);">
    {{-- ambient depth --}}
    <div class="pointer-events-none absolute -left-24 top-40 h-72 w-72 rounded-full bg-sky-400/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-24 bottom-24 h-80 w-80 rounded-full bg-navy-500/25 blur-3xl"></div>
    <div class="pointer-events-none absolute inset-0 grain opacity-20"></div>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>

    {{-- Logo besar samar di pojok kanan atas.

         Berkas logonya memuat segitiga di atas dan tulisan "DELTA TIGA ENAM"
         emas di bawah. Tulisannya tidak ikut ditampilkan: pembungkusnya dipatok
         pada rasio segitiganya saja (1600:1310 dari 1600x1588 piksel aslinya)
         lalu overflow-hidden memotong sisanya, jadi yang tersisa hanya bentuk
         segitiganya. Memperkecil opasitas saja tidak cukup — tulisannya akan
         tetap terbaca samar dan mengulang nama yang sudah tertulis di kolom
         kiri.

         Mulai lg tingginya yang jadi patokan, bukan lebarnya: top-0 dan
         bottom-0 membuatnya setinggi footer, lalu rasio tadi yang menghitung
         lebarnya. Jadi alas segitiganya berhenti tepat di tepi bawah footer
         berapa pun tinggi isinya — lebar tetap yang dipakai sebelumnya selalu
         meleset begitu jumlah baris di kolom Kontak berubah.

         Di bawah lg lebarnya tetap yang jadi patokan. Di layar sempit
         kolomnya menumpuk sehingga footer jadi jauh lebih tinggi daripada
         lebarnya, dan logo setinggi itu akan melebar sampai menutupi seluruh
         isi footer.

         aria-hidden + alt kosong: ini ornamen, dan pembaca layar sudah
         mendapat nama perusahaan dari tautan logo di kolom brand. --}}
    <div aria-hidden="true"
         class="pointer-events-none absolute -right-20 -top-10 aspect-[1600/1310] w-[22rem] overflow-hidden opacity-[0.07] sm:-right-24 sm:w-[30rem] lg:-right-28 lg:bottom-0 lg:top-0 lg:h-full lg:w-auto">
        <img src="{{ asset('images/logodelta36.png') }}" alt="" loading="lazy" class="h-auto w-full max-w-none">
    </div>

    {{-- Kolom footer. Dulu ditarik naik dengan -mt-24 lalu didorong balik
         dengan pt-32 supaya kartu ajakan bisa menimpa tepi atasnya; tanpa
         kartu itu keduanya tidak ada gunanya lagi dan jaraknya kembali
         sederhana. --}}
    <div class="relative z-10">
        <div class="container py-16 md:py-20">
            <div class="grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-12">

                {{-- Brand. Layanan dan Tautan sekarang berdiri sebagai kolom
                     tersendiri, bukan satu kolom berisi dua daftar, sehingga
                     keempatnya sejajar dalam satu baris: 3-2-2-5. Kontak yang
                     paling berisi dapat porsi terlebar, Layanan dan Tautan yang
                     hanya daftar pendek cukup sempit. --}}
                <div class="space-y-6 sm:col-span-2 lg:col-span-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('images/logodelta36.png') }}" alt="PT. Delta Tiga Enam" class="h-10 w-10 shrink-0">
                        <span class="font-display text-lg font-semibold tracking-wide text-white">PT. Delta Tiga Enam</span>
                    </a>
                    <p class="max-w-sm text-sm leading-relaxed text-white/70">
                        {{ $id
                            ? 'Sertifikasi, pelatihan, penyeleksian, dan penempatan tenaga kerja di Indonesia.'
                            : 'Certification, training, selection, and placement of workforce in Indonesia.' }}
                    </p>
                    <div class="flex items-center gap-3 pt-1">
                        <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="grid h-11 w-11 place-items-center rounded-full bg-white/10 text-white ring-1 ring-white/15 backdrop-blur-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-gold hover:text-navy-950 hover:ring-gold-soft" aria-label="LinkedIn">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                        <a href="mailto:{{ $email }}" class="grid h-11 w-11 place-items-center rounded-full bg-white/10 text-white ring-1 ring-white/15 backdrop-blur-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-gold hover:text-navy-950 hover:ring-gold-soft" aria-label="Email">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        </a>
                        <a href="{{ $facebook }}" target="_blank" rel="noopener" class="grid h-11 w-11 place-items-center rounded-full bg-white/10 text-white ring-1 ring-white/15 backdrop-blur-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-gold hover:text-navy-950 hover:ring-gold-soft" aria-label="Facebook">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="{{ $instagram }}" target="_blank" rel="noopener" class="grid h-11 w-11 place-items-center rounded-full bg-white/10 text-white ring-1 ring-white/15 backdrop-blur-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-gold hover:text-navy-950 hover:ring-gold-soft" aria-label="Instagram">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Layanan --}}
                <div class="lg:col-span-2">
                    <p class="text-sm font-semibold text-gold-soft">{{ $id ? 'Layanan' : 'Services' }}</p>
                    <ul class="mt-5 space-y-3 text-sm">
                        @forelse ($footerServices as $svc)
                            <li><a href="{{ route('services.index') }}#{{ $svc->slug }}" class="group -my-1.5 inline-flex items-center py-1.5 text-white/75 transition-colors hover:text-gold">
                                <span class="h-px w-0 bg-gradient-to-r from-gold to-gold-soft transition-all duration-300 group-hover:mr-2.5 group-hover:w-4"></span>{{ $svc->name }}
                            </a></li>
                        @empty
                            <li><a href="{{ route('services.index') }}" class="group -my-1.5 inline-flex items-center py-1.5 text-white/75 transition-colors hover:text-gold">
                                <span class="h-px w-0 bg-gradient-to-r from-gold to-gold-soft transition-all duration-300 group-hover:mr-2.5 group-hover:w-4"></span>{{ $id ? 'Semua Layanan' : 'All Services' }}
                            </a></li>
                        @endforelse
                    </ul>
                </div>

                {{-- Tautan --}}
                <div class="lg:col-span-2">
                    <p class="text-sm font-semibold text-gold-soft">{{ __('site.common.quick_links') }}</p>
                    <ul class="mt-5 space-y-3 text-sm">
                        @foreach ($nav as $route => $label)
                            <li><a href="{{ route($route) }}" class="group -my-1.5 inline-flex items-center py-1.5 text-white/75 transition-colors hover:text-gold">
                                <span class="h-px w-0 bg-gradient-to-r from-gold to-gold-soft transition-all duration-300 group-hover:mr-2.5 group-hover:w-4"></span>{{ $label }}
                            </a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Kontak. Blok "Kantor Kami" yang dulu berdiri sendiri selebar
                     footer di bawah ketiga kolom ini kini masuk ke sini: isinya
                     memang alamat dan nomor telepon, yaitu kontak — berdiri
                     terpisah, ia mengulang kolom ini dengan alamat yang sama
                     ditulis dua versi, ringkas di atas dan panjang di bawah.

                     Tiap kantor dapat satu ikon lokasi dan satu ikon telepon,
                     dengan namanya sebagai penanda di atasnya. Alamatnya mengalir
                     mengikuti lebar kolom, tidak lagi dipatah dengan <br>: kolom
                     ini jauh lebih sempit daripada blok selebar footer tadi,
                     sehingga patahan yang dipatok akan jatuh di tengah nama
                     jalan. --}}
                <div class="sm:col-span-2 lg:col-span-5">
                    <p class="text-sm font-semibold text-gold-soft">{{ $id ? 'Kontak' : 'Contact' }}</p>

                    <div class="mt-5 space-y-5">
                        @foreach ($offices as $office)
                            <div @class(['border-t border-white/10 pt-5' => ! $loop->first])>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gold-soft">{{ $office['name'] }}</p>
                                <ul class="mt-2.5 space-y-2 text-sm text-white/80">
                                    <li class="flex items-start gap-2.5">
                                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-white/45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <span class="leading-relaxed">{{ $office['address'] }}</span>
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <svg class="h-4 w-4 shrink-0 text-white/45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <a href="tel:{{ $office['tel'] }}" class="transition-colors hover:text-gold">{{ $office['phone'] }}</a>
                                    </li>
                                </ul>
                            </div>
                        @endforeach

                        {{-- Surel berdiri di luar kedua kantor: ia satu untuk
                             keduanya, bukan milik salah satunya. --}}
                        <div class="flex items-center gap-2.5 border-t border-white/10 pt-5 text-sm text-white/80">
                            <svg class="h-4 w-4 shrink-0 text-white/45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <a href="mailto:{{ $email }}" class="transition-colors hover:text-gold">{{ $email }}</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="mt-12 flex flex-col items-start justify-between gap-4 border-t border-white/15 pt-7 text-xs text-white/60 md:flex-row md:items-center">
                <p>© {{ $year }} PT. Delta Tiga Enam. {{ $id ? 'Hak Cipta Dilindungi Undang-Undang.' : 'All rights reserved.' }}</p>
                <div class="flex items-center gap-5">
                    <a href="#" class="transition-colors hover:text-gold">{{ $id ? 'Kebijakan Privasi' : 'Privacy Policy' }}</a>
                    {{-- Garis, bukan huruf "|": pemisah ini tidak membawa makna, dan
                         sebagai teks ia hanya 2,2:1 sekaligus ikut dibacakan pembaca
                         layar. --}}
                    <span class="h-3 w-px bg-white/30" aria-hidden="true"></span>
                    <a href="#" class="transition-colors hover:text-gold">{{ $id ? 'Syarat & Ketentuan' : 'Terms & Conditions' }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
