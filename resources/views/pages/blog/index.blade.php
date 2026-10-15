@php
    use Illuminate\Support\Facades\Storage;
    $id = app()->getLocale() === 'id';
    $imgUrl = fn ($v) => $v ? (str_starts_with($v, 'http') ? $v : Storage::url($v)) : null;
@endphp

<x-layout :title="__('site.nav.blog')">
    <x-page-header
        :title="__('site.home.blog_title')"
        :subtitle="$id ? 'Perspektif, riset, dan praktik terbaik seputar human capital.' : 'Perspectives, research, and best practices on human capital.'"
        placement="blog"
        image="photo-1499750310107-5fef28a66643" />

    <section id="{{ \App\Http\Controllers\Controller::RESULTS_ANCHOR }}" class="section scroll-mt-28 bg-white">
        <div class="container">
            {{-- Kepala daftar — judul, pengantar, pencarian, jumlah hasil. Blok
                 yang sama dipakai halaman Portofolio, jadi keduanya tidak bisa
                 menyimpang lagi seperti sebelumnya. --}}
            <x-list-header
                {{-- Judulnya utuh di 'lead', tanpa 'accent': slot aksen selalu
                     membawa .italic-accent beserta warnanya, dan di sini judulnya
                     dikehendaki tegak seluruhnya dalam satu warna. --}}
                :lead="$id ? 'Wawasan & Artikel' : 'Insights & Articles'"
                :intro="$id
                    ? 'Catatan dari ruang pelatihan, lokasi uji kompetensi, dan meja konsultasi kami — ditulis dari pekerjaan yang benar-benar kami kerjakan, bukan rangkuman bacaan.'
                    : 'Notes from our training rooms, assessment sites, and consulting desks — written from work we have actually done, not summarised from reading.'"
                search-id="blog-q"
                :search-action="route('blog.index')"
                :search-value="$q ?? ''"
                :search-placeholder="$id ? 'Cari artikel…' : 'Search articles…'"
                :search-label="$id ? 'Cari artikel' : 'Search articles'"
                :total="$posts->total()"
                :noun="$id ? 'artikel' : 'articles'" />

            {{-- Uniform grid — every card the same height (auto-rows-fr), 3 across then wraps down --}}
            @if ($posts->count())
                <div class="grid auto-rows-fr gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <x-blog-card :post="$post" :delay="($loop->index % 3) * 50" />
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-navy-200 bg-neutral-50 p-16 text-center">
                    {{-- text-gold, sama dengan ikon keadaan kosong di halaman
                         Portofolio dan Layanan. Sebelumnya sky-400, dan halaman
                         daftar di situs ini jadi punya dua rupa untuk keadaan
                         yang sama persis. 8,19:1 di atas navy. --}}
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-navy text-gold">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M6 4h9l3 3v13H6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M9 9h6M9 13h6M9 17h4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                    </span>
                    <p class="mt-5 font-display text-lg text-navy">{{ ($q ?? '') !== '' ? ($id ? 'Tidak ada hasil' : 'No results found') : ($id ? 'Belum ada artikel' : 'No articles yet') }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ ($q ?? '') !== '' ? ($id ? 'Coba kata kunci lain atau lihat semua artikel.' : 'Try a different keyword or view all articles.') : ($id ? 'Artikel & wawasan terbaru akan tampil di sini.' : 'Our latest articles and insights will appear here.') }}</p>
                    @if (($q ?? '') !== '')
                        <a href="{{ route('blog.index') }}" class="btn-blue mt-6">{{ $id ? 'Lihat Semua Artikel' : 'View All Articles' }}</a>
                    @endif
                </div>
            @endif

            @if ($posts->hasPages())
                <div class="mt-16">{{ $posts->links('pagination.brand') }}</div>
            @endif
        </div>
    </section>
</x-layout>
