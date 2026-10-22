@props([
    'name' => 'phone',
    'label' => null,
    'required' => false,
    'value' => null,
])

{{--
    Kolom nomor telepon berkode negara, bawaan +62.

    Dua kolom yang terlihat, satu nilai yang terkirim. Pemilih negara dan
    kolom angka sengaja TIDAK punya atribut name; yang dikirim ke peladen
    adalah input tersembunyi di bawahnya, berisi gabungan keduanya. Dengan
    begitu kontrak di sisi peladen tidak berubah sama sekali — pengontrolnya
    tetap menerima satu kolom 'phone' seperti sebelumnya, dan aturan
    App\Rules\PhoneNumber tetap berlaku apa adanya.

    Nol di depan dibuang saat digabung. Orang Indonesia mengetik nomornya
    "0818834766", dan digabung mentah dengan +62 ia menjadi "+62 0818834766" —
    bentuk yang ditolak jaringan mana pun. Yang benar membuang nol itu.

    Nomor yang sudah tersimpan dipecah kembali oleh DialCodes::split supaya
    formulir yang dimuat ulang setelah validasi gagal tidak menumpuk kode
    negaranya jadi "+62 +62 818…".
--}}
@php
    $isId = app()->getLocale() === 'id';
    [$kodeAwal, $nomorAwal] = \App\Support\DialCodes::split(old($name, $value));
    $invalid = $errors->has($name);
    $negara = \App\Support\DialCodes::all();
@endphp

<div data-field-box="{{ $name }}"
     x-data="{
         cc: @js($kodeAwal),
         lokal: @js($nomorAwal),
         buka: false,
         cari: '',
         negara: @js(collect($negara)->map(fn ($n) => ['k' => $n['kode'], 'n' => $n['nama']])->values()),
         get tersaring() {
             const q = this.cari.trim().toLowerCase().replace(/^\+/, '');
             if (q === '') return this.negara;
             return this.negara.filter((x) => x.n.toLowerCase().includes(q) || x.k.replace('+', '').startsWith(q));
         },
         pilih(k) {
             this.cc = k;
             this.buka = false;
             this.cari = '';
             this.$nextTick(() => this.$refs.nomor.focus());
         },
         get gabungan() {
             const mentah = this.lokal.trim();

             /* Diawali tanda plus berarti pengisinya sudah menuliskan nomor
                internasional lengkap. Kode dari pemilih di sebelahnya tidak
                ditempelkan lagi — tanpa pengecualian ini, "+65 9123 4567"
                yang diketik di kolom ini menjadi "+65 6591234567", kode
                negaranya tertulis dua kali. Plus adalah satu-satunya isyarat
                yang tidak meragukan: angka 65 di awal nomor bisa saja memang
                bagian dari nomornya, tetapi tanda plus tidak pernah. */
             if (mentah.startsWith('+')) {
                 const internasional = mentah.replace(/[^\d]/g, '');

                 return internasional === '' ? '' : '+' + internasional;
             }

             /* Diperas jadi angka saja sebelum digabung, bukan sekadar
                dibuang nol depannya. Membuang nol dari teks mentah gagal
                begitu nomornya diketik dengan kurung: "(021) 5890 5002"
                diawali kurung, bukan nol, jadi nolnya lolos dan hasilnya
                "+62 (021) 5890 5002" — kode negara ditempel pada nomor yang
                masih memakai kode wilayah lokalnya, bentuk yang tidak bisa
                dihubungi dari mana pun. Diperas lebih dulu, ia menjadi
                "+62 2158905002". */
             const n = mentah.replace(/\D/g, '').replace(/^0+/, '');

             return n === '' ? '' : this.cc + ' ' + n;
         },
     }">

    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-navy">
        {{ $label ?? ($isId ? 'Nomor Telepon' : 'Phone Number') }}@if ($required)<span class="text-sky-700" aria-hidden="true"> *</span>@endif
    </label>

    <div class="flex gap-2">
        {{-- Pemilih kode negara buatan sendiri, bukan <select> bawaan.

             <select> menampilkan teks yang SAMA saat tertutup dan terbuka,
             jadi pilihannya hanya dua: tertutup ia memuat nama negara yang
             panjang ("+62 · Indonesia") dan mendesak kolom nomor di
             sebelahnya, atau terbuka ia hanya berisi deretan angka kode yang
             tidak bisa dicari orang yang tidak hafal kode negaranya. Di sini
             tertutup cukup "+62", terbuka berisi nama negara yang bisa dicari.

             Tetap sepenuhnya bisa dipakai lewat papan tik: Enter memilih hasil
             pencarian teratas, Escape menutup, dan fokus kembali ke kolom
             nomor setelah memilih. --}}
        <div class="relative shrink-0" x-on:click.outside="buka = false" x-on:keydown.escape="buka = false">
            <button type="button"
                    x-on:click="buka = ! buka; if (buka) $nextTick(() => $refs.cari.focus())"
                    x-bind:aria-expanded="buka.toString()"
                    aria-haspopup="listbox"
                    aria-label="{{ $isId ? 'Kode negara' : 'Country code' }}"
                    @class([
                        'flex h-full min-w-[5.75rem] items-center justify-between gap-2 rounded-2xl border bg-white px-3.5 py-3 font-mono text-sm font-medium text-navy shadow-sm transition-colors hover:border-navy-600 focus:outline-none focus:ring-2 focus:ring-sky-700',
                        'border-rose-400' => $invalid,
                        'border-navy-400' => ! $invalid,
                    ])>
                <span x-text="cc">{{ $kodeAwal }}</span>
                <svg class="h-3.5 w-3.5 shrink-0 text-slate-500 transition-transform duration-200" x-bind:class="buka && 'rotate-180'" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="m4 6 4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <div x-show="buka" x-cloak
                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="absolute left-0 top-full z-30 mt-2 w-72 max-w-[calc(100vw-2.5rem)] overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-lift">
                <div class="border-b border-navy-100 p-2">
                    <input type="text" x-ref="cari" x-model="cari"
                           x-on:keydown.enter.prevent="tersaring.length && pilih(tersaring[0].k)"
                           placeholder="{{ $isId ? 'Cari negara atau kode' : 'Search country or code' }}"
                           aria-label="{{ $isId ? 'Cari negara' : 'Search country' }}"
                           class="w-full rounded-xl border border-navy-200 bg-white px-3 py-2 text-sm text-navy placeholder:text-slate-400 focus:border-navy-600 focus:outline-none focus:ring-2 focus:ring-sky-700">
                </div>
                <ul role="listbox" class="max-h-64 overflow-y-auto overscroll-contain py-1">
                    <template x-for="n in tersaring" x-bind:key="n.n">
                        <li role="option"
                            x-bind:aria-selected="(n.k === cc).toString()"
                            x-on:click="pilih(n.k)"
                            class="flex cursor-pointer items-center justify-between gap-3 px-3.5 py-2 text-sm transition-colors hover:bg-navy-50"
                            x-bind:class="n.k === cc ? 'bg-navy-50 font-medium text-navy-700' : 'text-slate-700'">
                            <span x-text="n.n"></span>
                            <span class="shrink-0 font-mono text-xs text-slate-500" x-text="n.k"></span>
                        </li>
                    </template>
                    <li x-show="tersaring.length === 0" class="px-3.5 py-3 text-sm text-slate-500">
                        {{ $isId ? 'Negara tidak ditemukan.' : 'No country found.' }}
                    </li>
                </ul>
            </div>
        </div>

        <input type="tel"
               id="{{ $name }}"
               x-model="lokal"
               x-ref="nomor"
               inputmode="tel"
               autocomplete="tel-national"
               @if ($required) required aria-required="true" @endif
               @if ($invalid) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
               placeholder="812 3456 7890"
               @class([
                   'w-full min-w-0 rounded-2xl border bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-700',
                   'border-rose-400' => $invalid,
                   'border-navy-400 focus:border-navy' => ! $invalid,
               ])>
    </div>

    {{-- Satu-satunya yang benar-benar terkirim. --}}
    <input type="hidden" name="{{ $name }}" x-bind:value="gabungan">

    <p class="mt-1.5 text-xs text-slate-500" x-show="lokal.trim() !== ''" x-cloak>
        {{ $isId ? 'Terkirim sebagai' : 'Submitted as' }} <span class="font-mono text-navy" x-text="gabungan"></span>
    </p>

    @error($name)
        <p id="{{ $name }}-error" class="field-note" role="alert">{{ $message }}</p>
    @enderror
</div>
