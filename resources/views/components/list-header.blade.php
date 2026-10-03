@props([
    'lead' => '',          // kata tegak sebelum aksen
    'accent' => '',        // kata miring berwarna
    'tail' => '',          // kata tegak sesudah aksen
    'intro' => null,       // kalimat pengantar di bawah judul
    'searchId',
    'searchAction',
    'searchValue' => '',
    'searchPlaceholder' => '',
    'searchLabel' => '',
    'total' => 0,          // jumlah seluruh hasil
    'noun' => '',          // satuan: "artikel", "proyek", …
])

{{--
    Kepala halaman daftar: judul beraksen, kalimat pengantar, kolom pencarian,
    dan jumlah hasil.

    Halaman Blog dan Portofolio menulis blok ini masing-masing, dan keduanya
    sudah menyimpang satu sama lain — judulnya text-3xl/md:text-4xl di satu
    tempat dan token text-display-lg di tempat lain, jumlah hasilnya text-xs
    lawan text-sm, tautan "reset"-nya sky-600 lawan sky-700, dan tanda kutipnya
    entitas HTML lawan huruf sungguhan. Satu komponen menutup celah itu dan
    membuat halaman daftar berikutnya ikut benar tanpa disalin.

    Pencarian berdiri di samping kanan tulisannya, bukan di baris sendiri di
    bawah garis pemisah. Keduanya diratakan ke garis bawah (md:items-end),
    jadi kolom pencarian sejajar dengan akhir kalimat pengantar berapa pun
    jumlah barisnya — perataan ke atas akan melepaskannya menggantung begitu
    judulnya lebih dari dua baris.

    Di bawah md keduanya menumpuk: kolom kanan hanya selebar 18rem, dan
    memaksanya berdampingan dengan judul di layar sempit menyisakan dua kolom
    yang sama-sama terlalu sempit.
--}}

@php $isId = app()->getLocale() === 'id'; @endphp

<div class="mb-12 flex flex-col gap-6 md:flex-row md:items-end md:justify-between md:gap-10">
    <div class="max-w-2xl">
        <x-heading
            :lead="$lead"
            :accent="$accent"
            :tail="$tail"
            class="text-display-lg text-navy"
            data-aos="fade-up" />
        @if ($intro)
            <p class="mt-5 text-pretty leading-relaxed text-slate-600" data-aos="fade-up">{{ $intro }}</p>
        @endif
    </div>

    {{-- Jumlah hasil di bawah kolom pencarian, dirapatkan ke kanan mengikuti
         kolomnya mulai md. --}}
    <div class="flex shrink-0 flex-col items-start gap-2.5 md:items-end" data-aos="fade-up">
        <x-search-form
            :id="$searchId"
            :action="$searchAction"
            :value="$searchValue"
            :placeholder="$searchPlaceholder"
            :label="$searchLabel" />

        {{-- slate-600, bukan slate-500: pada 12px ambangnya 4,5:1, dan slate-500
             hanya 4,76:1 di atas putih — tanpa sisa ruang kalau latarnya nanti
             diberi tinta.

             Tautan "atur ulang" membawa fragmen hasil, sama seperti formulir
             pencariannya. Tanpa itu mencari tetap menjaga posisi baca tapi
             menghapus pencarian justru melempar pembaca kembali ke atas hero. --}}
        @if ($searchValue !== '')
            <p class="font-mono text-xs text-slate-600">
                {{ $total }} {{ $isId ? 'hasil untuk' : 'results for' }} “{{ $searchValue }}”
                <span class="mx-1 text-navy-200" aria-hidden="true">·</span>
                <a href="{{ $searchAction }}#{{ \App\Http\Controllers\Controller::RESULTS_ANCHOR }}" class="text-sky-700 underline-offset-4 hover:underline">{{ $isId ? 'atur ulang' : 'reset' }}</a>
            </p>
        @elseif ($total)
            <p class="font-mono text-xs text-slate-600">
                <span class="text-sm font-semibold text-navy">{{ str_pad($total, 2, '0', STR_PAD_LEFT) }}</span>
                {{ $noun }}
            </p>
        @endif
    </div>
</div>
