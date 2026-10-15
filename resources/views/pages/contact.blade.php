@php
    $id = app()->getLocale() === 'id';

    $email = 'info@deltatigaenam.com';

    $waNumber = '62818834766';
    $waText = rawurlencode($id ? 'Halo Delta Tiga Enam, saya ingin bertanya seputar layanan Anda.' : 'Hello Delta Tiga Enam, I would like to ask about your services.');

    $contactRows = [
        ['label' => 'Email', 'value' => $email, 'href' => 'mailto:'.$email, 'icon' => 'mail'],
        ['label' => 'WhatsApp', 'value' => '0818 834 766', 'href' => 'https://wa.me/'.$waNumber.'?text='.$waText, 'icon' => 'whatsapp'],
        ['label' => $id ? 'Telepon' : 'Phone', 'value' => '(021) 5890 5002', 'href' => 'tel:+622158905002', 'icon' => 'phone'],
        ['label' => $id ? 'Jam Operasional' : 'Office Hours', 'value' => $id ? 'Senin – Jumat · 08.00–17.00' : 'Mon – Fri · 8am–5pm', 'href' => null, 'icon' => 'clock'],
    ];
@endphp

<x-layout :title="__('site.nav.contact')">
    {{-- Tanpa :eyebrow. .eyebrow bernilai display:none !important di seluruh
         situs ini, jadi "PT Delta Tiga Enam" tidak pernah tampil — yang tersisa
         hanya simpul DOM mati beserta satu pendaftaran pengamat AOS. --}}
    <x-page-header
        :title="__('site.contact.title')"
        :subtitle="$id ? 'Ceritakan kebutuhan organisasi Anda. Tim kami akan merespons dengan cepat.' : 'Tell us about your needs — our team will respond promptly.'"
        placement="contact"
        image="photo-1497366754035-f200968a6e72" />

    {{-- ===================== CONTACT + FORM ===================== --}}
    <section class="section relative overflow-hidden bg-white">
        <div class="pointer-events-none absolute inset-0 aurora-light opacity-60"></div>

        <div class="container relative grid gap-12 lg:grid-cols-12 lg:gap-16">

            {{-- Left: intro + quick contact --}}
            <div class="lg:col-span-5" data-aos="fade-right">
                {{-- Label "Hubungi Kami" di atas judul ikut dilepas, alasan yang sama.

                     Ukurannya memakai token text-display-lg, bukan text-4xl/md:text-5xl
                     yang ditulis sendiri: yang kedua melompat sekali di satu titik
                     putus, sedangkan tokennya clamp() yang mengalir, dan judul seksi
                     kedua di halaman ini sudah memakai token itu. Dua skala judul
                     dalam satu halaman tidak ada yang menuntutnya.

                     font-display dan text-balance tidak ditulis: app.css sudah
                     memberikannya ke setiap h1–h4. font-bold ditulis karena memang
                     menimpa font-semibold bawaan itu. --}}
                {{-- Seluruh judul masuk ke 'lead', tanpa 'accent'. Slot aksen di
                     <x-heading> selalu membawa .italic-accent beserta warnanya,
                     dan di halaman ini keduanya tidak dikehendaki: judulnya tegak
                     seluruhnya, satu warna. --}}
                <x-heading
                    :lead="$id ? 'Ceritakan kebutuhan Anda' : 'Tell us what you need'"
                    class="text-display-lg font-bold text-navy" />
                {{-- Menyebut apa yang perlu ditulis, bukan menjanjikan kesiapan.
                     "Tim kami siap membantu" tidak menambah apa pun yang tidak
                     sudah tersirat dari adanya formulir; menyebut bidang dan
                     jumlah peserta justru membuat jawaban pertama kami langsung
                     berguna, dan itu menghemat satu putaran surat-menyurat. --}}
                <p class="mt-5 max-w-md text-pretty leading-relaxed text-slate-600">
                    {{ $id
                        ? 'Pelatihan, uji kompetensi, atau rekrutmen — isi formulir di samping atau hubungi kami langsung lewat nomor dan surel di bawah. Sebutkan bidangnya dan perkiraan jumlah peserta agar jawaban kami langsung tepat sasaran.'
                        : 'Training, competency assessment, or recruitment — use the form, or reach us directly on the number and email below. Mention the field and a rough headcount so our first reply is already useful.' }}
                </p>

                {{-- Satu panel bergaris pemisah, bukan empat kartu terpisah.
                     Empat kartu sejajar dengan ikon-label-nilai yang sama persis
                     adalah bentuk yang justru dihindari PRODUCT.md, dan empat
                     ubin gradien biru berturut-turut membuat kolom ini lebih
                     ramai daripada formulir di sebelahnya — padahal formulir
                     itulah tujuan halaman ini. --}}
                {{-- .card sudah berisi rounded-2xl + border-navy-100 + bg-white +
                     shadow-card; ditulis ulang di sini berarti permukaan yang sama
                     punya dua sumber yang bisa menyimpang. overflow-hidden tetap
                     ditulis: tanpa itu latar sentuh baris pertama dan terakhir
                     menyiku di sudut panel. --}}
                <div class="card mt-9 divide-y divide-navy-100 overflow-hidden">
                    @foreach ($contactRows as $row)
                        {{-- Nama tagnya dihitung sekali. Sebelumnya syarat yang sama
                             diuji di tag buka dan tag tutup sebagai dua tulisan
                             terpisah, dan tidak ada yang menjaga keduanya tetap sama. --}}
                        @php $tag = $row['href'] ? 'a' : 'div'; @endphp
                        <{{ $tag }} @if ($row['href']) href="{{ $row['href'] }}" @endif
                            @if ($row['href'] && str_starts_with($row['href'], 'http')) target="_blank" rel="noopener" @endif
                            class="group flex items-center gap-4 px-5 py-4 {{ $row['href'] ? 'transition-colors duration-300 hover:bg-navy-50/60' : '' }}">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sky-50 text-sky-700 {{ $row['href'] ? 'transition-colors duration-300 group-hover:bg-navy-600 group-hover:text-white' : '' }}">
                                @if ($row['icon'] === 'mail')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                @elseif ($row['icon'] === 'whatsapp')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.2 4.79 1.2h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.8 14.16c-.24.68-1.42 1.31-1.95 1.36-.5.05-.96.24-3.23-.67-2.73-1.08-4.47-3.86-4.6-4.04-.14-.18-1.11-1.48-1.11-2.82 0-1.34.7-2 .95-2.28.24-.27.53-.34.7-.34.18 0 .35 0 .5.01.16.01.38-.06.59.45.22.52.73 1.8.8 1.93.06.13.11.28.02.46-.09.18-.13.29-.26.45-.13.16-.28.36-.4.48-.13.13-.27.28-.12.54.15.26.66 1.09 1.42 1.76.97.87 1.79 1.14 2.05 1.27.26.13.41.11.56-.07.15-.18.65-.76.82-1.02.17-.26.35-.22.59-.13.24.09 1.52.72 1.78.85.26.13.43.2.5.31.06.11.06.64-.18 1.32z"/></svg>
                                @elseif ($row['icon'] === 'phone')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                @else
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="font-mono text-[10px] uppercase tracking-normal text-slate-500">{{ $row['label'] }}</p>
                                <p class="mt-0.5 truncate text-sm font-semibold text-navy">{{ $row['value'] }}</p>
                            </div>
                            @if ($row['href'])
                                <svg class="ml-auto h-4 w-4 shrink-0 text-slate-500 transition duration-300 group-hover:translate-x-0.5 group-hover:text-sky-700" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @endif
                        </{{ $tag }}>
                    @endforeach
                </div>
            </div>

            {{-- Right: form --}}
            <div class="lg:col-span-7" data-aos="fade-left">
                <div class="rounded-3xl border border-navy-100 bg-white p-8 shadow-lift md:p-10">
                    {{-- Label "Kirim Pesan" di atas judul dilepas dengan alasan yang
                         sama seperti di kolom kiri: .eyebrow tidak pernah tampil. --}}
                    <h2 class="mb-7 text-2xl font-bold text-navy md:text-3xl">{{ $id ? 'Kirim pesan Anda' : 'Send your message' }}</h2>

                    @if (session('status'))
                        <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800" role="status" aria-live="polite">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l5-5z" clip-rule="evenodd"/></svg>
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-6">
                        @csrf
                        <div style="display:none !important" aria-hidden="true">
                            <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-field name="name" :label="__('site.contact.name')" required />
                            <x-field name="email" type="email" :label="__('site.contact.email')" required />
                            <x-field name="phone" :label="__('site.contact.phone')" />
                            <x-field name="subject" :label="__('site.contact.subject')" />
                        </div>
                        <x-field name="message" :label="__('site.contact.message')" type="textarea" required />

                        <div class="flex flex-col gap-4 pt-2 sm:flex-row sm:items-center sm:justify-between">
                            <p class="flex items-center gap-2 font-mono text-[11px] text-slate-500">
                                <svg class="h-4 w-4 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                {{ $id ? 'Data Anda aman & terlindungi.' : 'Your data is safe & protected.' }}
                            </p>
                            <button type="submit" class="btn-blue group w-full justify-center sm:w-auto">
                                <span>{{ __('site.cta.send') }}</span>
                                <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== OFFICES + MAP ===================== --}}
    {{-- bg-paper, bukan bg-neutral-50: seluruh situs ini memakai off-white
         bertinta biru untuk irama antar-seksi, dan satu abu-abu netral di
         sini berdiri sendiri di antaranya. --}}
    <section class="section-sm relative overflow-hidden border-t border-navy-50 bg-paper" x-data="{ activeOffice: 0, offices: {{ json_encode($offices) }} }">
        <div class="pointer-events-none absolute inset-0 aurora-light opacity-50"></div>

        <div class="container relative">
            <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                {{-- "Alamat & Peta Kantor", bukan "Alamat & Lokasi Kantor": salah
                     satu kartu di bawahnya kini bernama "Lokasi Kantor", dan judul
                     yang mengandung nama kartunya membuat keduanya terbaca sebagai
                     hal yang sama. --}}
                <h2 class="text-display-lg text-navy">{{ $id ? 'Alamat & Peta Kantor' : 'Office Addresses & Map' }}</h2>
            </div>

            <div class="mt-12 grid items-stretch gap-6 lg:grid-cols-12">
                {{-- Deretan kartu inilah yang menentukan tinggi baris, dan petanya
                     yang mengikuti — bukan sebaliknya. Sebelumnya petanya dipatok
                     380px lalu kartunya diregangkan supaya tidak menyisakan ruang
                     kosong; angka itu ikut jumlah kantor yang kebetulan ada
                     sekarang, jadi begitu kantornya bertambah atau berkurang
                     regangannya salah lagi. --}}
                <div class="flex flex-col gap-4 lg:col-span-5" data-aos="fade-up">
                    @foreach ($offices as $index => $office)
                        {{-- transition, bukan transition-all: yang berubah hanya warna
                             garis tepi dan bayangannya, dan transition-all ikut
                             mengawasi tinggi serta lebar tombol saat tata letaknya
                             dihitung ulang. --}}
                        <button type="button" x-on:click="activeOffice = {{ $index }}"
                                class="group flex flex-col gap-2.5 rounded-2xl border bg-white p-5 text-left transition duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-700"
                                x-bind:class="activeOffice === {{ $index }} ? 'border-navy-600 shadow-lift' : 'border-navy-100 hover:border-navy-200 hover:shadow-card'">
                            <div class="flex items-center gap-3">
                                {{-- Satu penanda terpilih, bukan tiga. Warna garis tepi,
                                     ubin ikon, dan titik kecil di kanan dulu menyatakan
                                     hal yang sama persis; titiknya yang paling sulit
                                     dilihat, jadi itu yang dilepas. --}}
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl transition-colors duration-300"
                                      x-bind:class="activeOffice === {{ $index }} ? 'bg-navy-600 text-white' : 'bg-sky-50 text-sky-700 group-hover:bg-navy-100'">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M10 21v-4h4v4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <span class="text-[17px] font-semibold text-navy">{{ $office['name'] }}</span>
                            </div>
                            <p class="text-sm leading-relaxed text-slate-600">{{ $office['address'] }}</p>
                            <div class="mt-1 flex items-center gap-2 border-t border-navy-100 pt-3 font-mono text-[11px] text-slate-500">
                                <svg class="h-3.5 w-3.5 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-3 2a12 12 0 005 5l2-3 5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/></svg>
                                {{ $office['phone'] }}
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Live map. Mulai lg tingginya mengikuti kolom kartu lewat h-full
                     (grid-nya items-stretch), jadi tidak ada angka piksel yang harus
                     ditebak. Di bawah lg h-full tidak punya acuan karena grid-nya
                     satu kolom, sehingga di sana ia memakai rasio 4:3. Angka 380px
                     dulu ditulis dua kali — di pembungkus dan di iframe — dan dua
                     salinan seperti itu bisa berbeda tanpa ada yang menyadarinya. --}}
                <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="100">
                    <div class="aspect-[4/3] overflow-hidden rounded-3xl border border-navy-100 shadow-lift lg:aspect-auto lg:h-full">
                        <iframe
                            x-bind:src="'https://www.google.com/maps?q=' + encodeURIComponent(offices[activeOffice].maps) + '&output=embed'"
                            class="h-full w-full border-0"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            x-bind:title="offices[activeOffice].name"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
