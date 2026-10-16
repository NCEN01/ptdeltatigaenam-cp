@props(['partner'])

{{--
    Kartu mitra untuk seksi gelap "Sertifikasi resmi" di beranda.

    Dipisah dari <x-partners-clients> karena kartu yang sama kini dipakai dua
    kali di seksi itu: di kisi diam ketika mitranya empat atau kurang, dan di
    rel bergeser ketika lebih dari empat. Disalin ke keduanya, setiap
    penyesuaian logo, nama, atau nomor registrasi harus dikerjakan dua kali —
    dan yang terjadi di kode seperti itu bukan dua salinan yang selalu sama,
    melainkan satu yang diperbarui dan satu yang tertinggal.

    Berbeda dari <x-partner-plate>, yang dipakai halaman Kemitraan berlatar
    putih. Keduanya memang kartu mitra, tetapi bentuk, tinggi kotak logo, dan
    warna tulisannya ditentukan latar tempatnya berdiri; menyatukannya jadi
    satu komponen bercabang akan menghasilkan dua tampilan yang saling
    menumpangi di satu berkas.
--}}
@php
    $name = trim($partner->name);
    $href = trim((string) ($partner->website_url ?? ''));

    /* Kartunya jadi tautan hanya bila mitranya punya alamat situs. Tautan yang
       tidak menuju ke mana pun tetap disinggahi papan tik dan tidak melakukan
       apa-apa ketika ditekan. */
    $tag = $href !== '' ? 'a' : 'div';
@endphp

<{{ $tag }} {{ $attributes->class(['mitra-card flex flex-col gap-4 outline-none focus-visible:ring-2 focus-visible:ring-navy-200 focus-visible:ring-offset-4 focus-visible:ring-offset-navy-900']) }}
    @if ($href !== '') href="{{ $href }}" target="_blank" rel="noopener" @endif>

    {{-- flex + h-full/w-full pada gambarnya, bukan grid + max-h-full.
         Persen tinggi pada butir grid yang barisnya auto tidak selalu punya
         acuan pasti, dan di situ max-height jatuh ke none: logo yang tingginya
         mendekati lebarnya lalu tampil seukuran aslinya dan meluber menimpa
         nama di bawahnya. Flex berjarak pasti menyelesaikannya, dan
         overflow-hidden menjaganya sekalipun ada logo dengan perbandingan tak
         terduga. --}}
    <div class="mitra-logo-box relative flex h-[150px] items-center justify-center overflow-hidden rounded-[14px] bg-white p-[22px]">
        @if ($partner->logo)
            {{-- alt kosong dengan sengaja: nama mitranya tertulis tepat di bawah
                 logo ini. Diisi, namanya terbaca dua kali oleh pembaca layar. --}}
            <img src="{{ Storage::url($partner->logo) }}" alt="" loading="lazy" decoding="async"
                 class="mitra-logo h-full w-full object-contain">
        @else
            <span class="font-display text-4xl font-semibold text-navy-200" aria-hidden="true">{{ mb_strtoupper(mb_substr($name, 0, 1)) }}</span>
        @endif
    </div>

    {{-- Nama lalu nomor, menurun. Sebelumnya nomornya sebaris dengan lingkaran
         panah di kanan; panahnya dilepas karena seluruh kartu memang sudah bisa
         diklik, dan lingkaran kecil yang hanya muncul saat disentuh tidak pernah
         sempat memberi tahu itu kepada orang yang belum menyentuhnya.

         Nomornya navy-200, bukan navy-400. Di atas panel kaca navy-400 hanya
         4,13:1 — di bawah ambang 4,5:1 untuk teks sekecil ini. navy-200
         mencapai 8,25:1 dan masih terbaca biru. --}}
    <div class="relative">
        <p class="font-display text-sm font-semibold leading-[1.45] text-white text-balance">{{ $name }}</p>
        @if ($partner->registration_number)
            <p class="mt-2 font-mono text-xs font-medium tracking-[0.1em] text-navy-200">{{ trim($partner->registration_number) }}</p>
        @endif
    </div>
</{{ $tag }}>
