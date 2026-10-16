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
        {{-- Lebar dipatok agar kolom angka di sebelahnya tidak menyusut
             mengikuti panjang nama negara yang sedang terpilih. --}}
        <select x-model="cc"
                aria-label="{{ $isId ? 'Kode negara' : 'Country code' }}"
                @class([
                    'w-[7.5rem] shrink-0 rounded-2xl border bg-white px-3 py-3 text-sm text-navy shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-sky-700',
                    'border-rose-400' => $invalid,
                    'border-navy-400 focus:border-navy' => ! $invalid,
                ])>
            @foreach ($negara as $n)
                {{-- Nama negara ikut ditulis di value-nya? Tidak: yang dikirim
                     hanya kode panggil, karena itulah satu-satunya bagian yang
                     berarti bagi nomor telepon. Dua negara boleh berbagi kode
                     yang sama (+1 untuk AS dan Kanada) dan itu tidak apa-apa. --}}
                <option value="{{ $n['kode'] }}">{{ $n['kode'] }} · {{ $n['nama'] }}</option>
            @endforeach
        </select>

        <input type="tel"
               id="{{ $name }}"
               x-model="lokal"
               inputmode="tel"
               autocomplete="tel-national"
               @if ($required) required aria-required="true" @endif
               @if ($invalid) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
               placeholder="{{ $isId ? '0818 834 766' : '812 3456 7890' }}"
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
