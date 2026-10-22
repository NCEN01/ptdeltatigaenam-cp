@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.portfolio')">
    <x-page-header
        :title="__('site.home.portfolio_title')"
        :subtitle="$id ? 'Kegiatan, klien, dan kisah dampak yang kami banggakan.' : 'Projects, clients, and the impact stories we are proud of.'"
        placement="portfolio"
        image="photo-1531403009284-440f080d1e12" />

    {{-- Portfolio grid (asymmetric).
         pb-0: the testimonial section below brings its own full top padding, so keeping
         this section's bottom padding too stacked two full gaps (288px at lg) between the
         pagination and the next heading. One section break is enough. --}}
    <section id="{{ \App\Http\Controllers\Controller::RESULTS_ANCHOR }}" class="section scroll-mt-28 pb-0">
        <div class="container">
            {{-- Kepala daftar yang sama dengan halaman Blog. Kalimat pengantarnya
                 menyebut pekerjaan yang benar-benar ada di daftar: keempat frasa
                 itu mencakup kelima kategori layanan yang masing-masing memang
                 berisi proyek, dan penutupnya menggambarkan susunan halaman
                 detailnya yang ditutup bagian Hasil berikut angkanya.

                 total(), bukan count(): count() hanya menghitung proyek di
                 halaman ini, bukan seluruh hasilnya. --}}
            {{-- Judulnya utuh di 'lead', tanpa 'accent': slot aksen selalu
                 membawa .italic-accent beserta warnanya, dan di sini judulnya
                 dikehendaki tegak seluruhnya dalam satu warna. Sama dengan
                 halaman Blog, yang memakai komponen ini juga. --}}
            <x-list-header
                :lead="$id ? 'Dampak Nyata bersama Klien' : 'Real Impact alongside Clients'"
                :intro="$id
                    ? 'Pelatihan, sertifikasi kompetensi, penataan organisasi, dan pencarian posisi kunci yang kami kerjakan bersama klien, ditulis lengkap sampai angka yang berubah setelahnya.'
                    : 'Training, competency certification, organisational restructuring, and executive search we have done alongside clients, written out in full down to the numbers that moved.'"
                search-id="portfolio-q"
                :search-action="route('portfolio.index')"
                :search-value="$q"
                :search-placeholder="$id ? 'Cari proyek, klien, lokasi…' : 'Search project, client, location…'"
                :search-label="$id ? 'Cari portofolio' : 'Search portfolio'"
                :total="$portfolios->total()"
                :noun="$id ? 'proyek' : 'projects'" />

            @if ($portfolios->isNotEmpty())
                <div class="grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($portfolios as $p)
                        @php $cover = $p->cover_image ? (str_starts_with($p->cover_image, 'http') ? $p->cover_image : Storage::url($p->cover_image)) : null; @endphp
                        <a href="{{ route('portfolio.show', $p->slug) }}" class="group block" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 50 }}">
                            {{-- Visual with hover reveal --}}
                            <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-navy-100 ring-1 ring-navy-100 transition-shadow duration-500 group-hover:shadow-lift">
                                @if ($cover)
                                    <img src="{{ $cover }}" alt="{{ $p->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-[900ms] ease-out-soft group-hover:scale-[1.07]">
                                @else
                                    <div class="absolute inset-0 aurora opacity-70"></div>
                                    <div class="absolute inset-0 grid place-items-center font-display text-6xl font-semibold text-white/85">{{ \Illuminate\Support\Str::substr($p->title, 0, 1) }}</div>
                                @endif

                                @if ($p->category)
                                    <span class="absolute left-4 top-4 z-10 rounded-full bg-white/90 px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-navy backdrop-blur">{{ $p->category->name }}</span>
                                @endif

                                {{-- reveal --}}
                                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950/85 via-navy-950/10 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                                <div class="pointer-events-none absolute inset-x-0 bottom-0 flex translate-y-3 items-center p-5 opacity-0 transition-all duration-500 ease-out-soft group-hover:translate-y-0 group-hover:opacity-100">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-semibold text-navy shadow-lift">
                                        {{ $id ? 'Lihat Detail' : 'View Case' }}
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                </div>
                            </div>

                            {{-- Title + small caption below --}}
                            <div class="mt-4 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-lg leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700">{{ $p->title }}</h3>
                                    <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-sm text-slate-500">
                                        @if ($p->client_name)<span>{{ $p->client_name }}</span>@endif
                                        @if ($p->client_name && $p->project_date)<span class="text-navy-200" aria-hidden="true">·</span>@endif
                                        @if ($p->project_date)<span class="font-mono text-xs">{{ $p->project_date->translatedFormat('Y') }}</span>@endif
                                        {{-- Reuses the same pin treatment as the blog cards. --}}
                                        <x-meta-location :value="$p->location" :separator="(bool) ($p->client_name || $p->project_date)" class="text-xs" />
                                    </p>
                                </div>
                                {{-- .card-arrow, sama dengan panah kartu Layanan, rel Agenda,
                                     dan indeks Portofolio di beranda. Putarannya pindah ke
                                     sana dari -rotate-45 milik Tailwind, dan isiannya dari
                                     sky-500 ke navy-600: putih di atas sky-500 hanya 2,37:1,
                                     jauh di bawah ambang mana pun, sementara navy-600 yang
                                     dipakai seluruh tombol situs ini mencapai 6,56:1. --}}
                                <span class="card-arrow mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-full border border-navy-200 text-navy group-hover:border-navy-600 group-hover:bg-navy-600 group-hover:text-white">
                                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($portfolios->hasPages())
                    <div class="mt-16">{{ $portfolios->links('pagination.brand') }}</div>
                @endif
            @else
                <div class="rounded-2xl border border-dashed border-navy-200 bg-mist p-16 text-center" data-aos="fade-up">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-navy text-gold">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M4 7h16v12H4z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M4 11h16M9 7V5h6v2" stroke="currentColor" stroke-width="1.4"/></svg>
                    </span>
                    {{-- Pencarian yang tidak menemukan apa pun berbeda dari portofolio yang
                         memang belum diisi: yang pertama butuh jalan keluar. --}}
                    @if ($q !== '')
                        <p class="mt-5 font-display text-lg font-semibold text-navy">{{ $id ? 'Tidak ada proyek yang cocok' : 'No matching projects' }}</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $id ? 'Coba nama klien, lokasi, atau sebagian judul proyeknya.' : 'Try a client name, a location, or part of the project title.' }}</p>
                        <a href="{{ route('portfolio.index') }}" class="btn-blue mt-6">{{ $id ? 'Tampilkan Semua Proyek' : 'Show All Projects' }}</a>
                    @else
                        <p class="mt-5 font-display text-lg font-semibold text-navy">{{ $id ? 'Portofolio segera hadir' : 'Portfolio coming soon' }}</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $id ? 'Kami sedang menyiapkan kisah proyek terbaik untuk ditampilkan di sini.' : 'We are preparing our best project stories to showcase here.' }}</p>
                        <a href="{{ route('contact.index') }}" class="btn-blue mt-6">{{ $id ? 'Diskusikan Proyek Anda' : 'Discuss Your Project' }}</a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- Testimonials --}}
    <x-testimonial-columns :testimonials="$testimonials" />

    {{-- Partners & Clients --}}
    <x-partners-clients :partners="$partners" :clients="$clients" />

</x-layout>
