@php
    $isId = app()->getLocale() === 'id';
    $offices = \App\Support\Company::offices();
    $email = 'info@deltatigaenam.com';

    // Daftar isi dan judul pasal dibaca dari satu larik. Nomor di daftar isi
    // dibangkitkan dari urutannya, jadi menambah atau memindah pasal tidak
    // bisa membuat penomoran daftar isi dan badan halaman berselisih.
    $toc = $isId ? [
        'ruang-lingkup'   => 'Ruang Lingkup',
        'pengendali'      => 'Pengendali Data Pribadi',
        'data'            => 'Data yang Kami Kumpulkan',
        'dasar'           => 'Dasar Pemrosesan',
        'tujuan'          => 'Tujuan Penggunaan',
        'cookie'          => 'Cookie',
        'pihak-ketiga'    => 'Pembagian kepada Pihak Ketiga',
        'retensi'         => 'Masa Penyimpanan',
        'keamanan'        => 'Keamanan Data',
        'hak-anda'        => 'Hak Anda',
        'cara-hak'        => 'Cara Menggunakan Hak Anda',
        'anak'            => 'Data Pribadi Anak',
        'perubahan'       => 'Perubahan Kebijakan',
        'kontak'          => 'Hubungi Kami',
    ] : [
        'ruang-lingkup'   => 'Scope',
        'pengendali'      => 'Data Controller',
        'data'            => 'Data We Collect',
        'dasar'           => 'Lawful Basis',
        'tujuan'          => 'Purposes of Use',
        'cookie'          => 'Cookies',
        'pihak-ketiga'    => 'Sharing with Third Parties',
        'retensi'         => 'Retention Period',
        'keamanan'        => 'Data Security',
        'hak-anda'        => 'Your Rights',
        'cara-hak'        => 'Exercising Your Rights',
        'anak'            => "Children's Data",
        'perubahan'       => 'Changes to This Policy',
        'kontak'          => 'Contact Us',
    ];
@endphp

<x-legal-page
    :title="$isId ? 'Kebijakan Privasi' : 'Privacy Policy'"
    :subtitle="$isId
        ? 'Bagaimana PT Delta Tiga Enam mengumpulkan, memakai, dan melindungi data pribadi Anda.'
        : 'How PT Delta Tiga Enam collects, uses, and protects your personal data.'"
    :updated="$isId ? '15 Oktober 2026' : '15 October 2026'"
    :intro="$isId
        ? 'Kebijakan ini menjelaskan data pribadi apa yang kami kumpulkan melalui situs ini, untuk apa data itu dipakai, kepada siapa data itu dibagikan, dan hak apa yang Anda miliki atasnya. Kami menyusunnya mengikuti Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi.'
        : 'This policy explains what personal data we collect through this site, what it is used for, who it is shared with, and what rights you hold over it. It follows Law Number 27 of 2022 on Personal Data Protection.'"
    :toc="$toc">

@if ($isId)

    <h2 id="ruang-lingkup">1. Ruang Lingkup</h2>
    <p>Kebijakan ini berlaku untuk seluruh data pribadi yang kami proses melalui situs <strong>deltatigaenam.com</strong> beserta seluruh subhalamannya, termasuk formulir hubungi kami, pendaftaran akun, pemesanan program pelatihan dan sertifikasi, serta pendaftaran peserta.</p>
    <p>Kebijakan ini tidak berlaku untuk situs atau layanan pihak lain yang mungkin tertaut dari situs kami. Begitu Anda meninggalkan situs ini, kebijakan privasi pihak tersebut yang berlaku.</p>
    <p>Dasar hukum yang kami rujuk:</p>
    <ul>
        <li>Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi;</li>
        <li>Undang-Undang Nomor 11 Tahun 2008 tentang Informasi dan Transaksi Elektronik sebagaimana diubah dengan Undang-Undang Nomor 19 Tahun 2016;</li>
        <li>Peraturan Pemerintah Nomor 71 Tahun 2019 tentang Penyelenggaraan Sistem dan Transaksi Elektronik.</li>
    </ul>

    <h2 id="pengendali">2. Pengendali Data Pribadi</h2>
    <p>Pengendali data pribadi atas pemrosesan yang dijelaskan dalam kebijakan ini adalah:</p>
    <p><strong>PT Delta Tiga Enam</strong></p>
    <ul>
        @foreach ($offices as $o)
            <li><strong>{{ $o['name'] }}</strong> — {{ $o['address'] }}@if (! empty($o['phone'])). Telepon: {{ $o['phone'] }}@endif</li>
        @endforeach
        <li>Surel: <a href="mailto:{{ $email }}">{{ $email }}</a></li>
    </ul>

    <h2 id="data">3. Data yang Kami Kumpulkan</h2>

    <h3>3.1 Data yang Anda berikan sendiri</h3>
    <p>Kami hanya menerima data ini apabila Anda mengisinya. Tidak ada kolom yang terisi diam-diam.</p>
    <ul>
        <li><strong>Formulir hubungi kami:</strong> nama, alamat surel, nomor telepon, subjek, dan isi pesan.</li>
        <li><strong>Pendaftaran akun:</strong> nama, alamat surel, kata sandi, nomor telepon, nama perusahaan, dan foto profil apabila Anda mengunggahnya.</li>
        <li><strong>Pemesanan program:</strong> nama, alamat surel, nomor telepon, nama perusahaan, jumlah peserta, dan catatan tambahan yang Anda tulis.</li>
        <li><strong>Data peserta:</strong> nama dan nomor telepon setiap peserta yang Anda daftarkan, serta catatan kehadirannya selama program berjalan.</li>
    </ul>
    <p>Kata sandi disimpan dalam bentuk teracak satu arah. Kami tidak dapat membacanya, dan tidak akan pernah meminta Anda menyebutkannya melalui surel, telepon, atau pesan.</p>

    <h3>3.2 Data yang terkumpul otomatis</h3>
    <ul>
        <li><strong>Catatan server:</strong> alamat IP, jenis dan versi peramban, sistem operasi, halaman yang dibuka, serta tanggal dan waktu akses. Catatan ini terbentuk otomatis pada setiap kunjungan dan kami gunakan untuk menjaga keamanan serta menelusuri gangguan teknis.</li>
        <li><strong>Cookie teknis:</strong> lihat bagian <a href="#cookie">Cookie</a>.</li>
    </ul>

    <h3>3.3 Data yang tidak kami kumpulkan</h3>
    <p>Kami tidak mengumpulkan data pribadi yang bersifat spesifik sebagaimana dimaksud Undang-Undang Pelindungan Data Pribadi — antara lain data kesehatan, data biometrik, data genetika, catatan kejahatan, pandangan politik, dan keyakinan agama. Mohon jangan mencantumkan data semacam itu pada kolom pesan atau catatan.</p>

    <h2 id="dasar">4. Dasar Pemrosesan</h2>
    <p>Setiap pemrosesan data pribadi yang kami lakukan berpijak pada salah satu dasar berikut:</p>
    <ul>
        <li><strong>Persetujuan Anda</strong> — saat Anda mengirim formulir hubungi kami atau mendaftarkan akun secara sukarela.</li>
        <li><strong>Pelaksanaan perjanjian</strong> — saat kami memproses pemesanan, menjalankan program pelatihan, dan menerbitkan sertifikat yang Anda pesan.</li>
        <li><strong>Pemenuhan kewajiban hukum</strong> — antara lain kewajiban perpajakan, pembukuan, dan pelaporan kepada lembaga sertifikasi yang berwenang.</li>
        <li><strong>Kepentingan yang sah</strong> — menjaga keamanan sistem, mencegah penyalahgunaan, dan memperbaiki mutu layanan, sepanjang tidak melampaui hak dan kepentingan Anda.</li>
    </ul>

    <h2 id="tujuan">5. Tujuan Penggunaan</h2>
    <p>Data pribadi Anda kami gunakan terbatas untuk:</p>
    <ul>
        <li>menjawab pertanyaan dan permintaan yang Anda sampaikan;</li>
        <li>membuat dan mengelola akun Anda di situs ini;</li>
        <li>memproses pendaftaran, pemesanan, penjadwalan, dan pembayaran program;</li>
        <li>menyelenggarakan pelatihan, uji kompetensi, dan penerbitan sertifikat, termasuk pencatatan kehadiran peserta;</li>
        <li>mengirimkan informasi yang berkaitan langsung dengan program yang Anda ikuti, seperti perubahan jadwal atau tempat;</li>
        <li>memenuhi kewajiban hukum dan menyelesaikan sengketa apabila timbul;</li>
        <li>menjaga keamanan situs dan mencegah penyalahgunaan.</li>
    </ul>
    <p><strong>Kami tidak memperjualbelikan data pribadi Anda kepada siapa pun.</strong> Kami juga tidak menggunakan data Anda untuk pengambilan keputusan yang sepenuhnya otomatis tanpa keterlibatan manusia.</p>

    <h2 id="cookie">6. Cookie</h2>
    <p>Situs ini hanya memasang cookie yang diperlukan agar situsnya berfungsi:</p>
    <ul>
        <li><strong>Cookie sesi</strong> — menjaga Anda tetap masuk selama berpindah halaman, dan terhapus saat sesi berakhir.</li>
        <li><strong>Cookie token keamanan</strong> — melindungi formulir dari pemalsuan permintaan lintas situs.</li>
        <li><strong>Preferensi bahasa</strong> — mengingat pilihan Bahasa Indonesia atau Inggris Anda.</li>
    </ul>
    <p>Kami <strong>tidak memasang cookie periklanan maupun cookie pelacak pihak ketiga</strong> di situs ini. Cookie teknis di atas dapat Anda hapus melalui pengaturan peramban, namun sebagian fungsi situs — terutama masuk ke akun — tidak akan bekerja tanpanya.</p>

    <h2 id="pihak-ketiga">7. Pembagian kepada Pihak Ketiga</h2>
    <p>Data pribadi Anda hanya kami bagikan sepanjang diperlukan, kepada:</p>
    <ul>
        <li><strong>Penyedia layanan pembayaran</strong> — untuk memproses transaksi pemesanan program. Data kartu atau rekening Anda diproses langsung oleh penyedia tersebut dan tidak pernah tersimpan di sistem kami.</li>
        <li><strong>Lembaga sertifikasi dan asesor</strong> — nama dan data peserta disampaikan kepada lembaga penerbit sertifikat sepanjang diperlukan untuk pelaksanaan uji kompetensi dan penerbitan sertifikat yang Anda pesan.</li>
        <li><strong>Penyedia infrastruktur</strong> — layanan hosting dan surel yang kami gunakan untuk menjalankan situs ini.</li>
        <li><strong>Aparat penegak hukum atau instansi berwenang</strong> — apabila diwajibkan oleh peraturan perundang-undangan atau putusan yang mengikat.</li>
    </ul>
    <p>Kami mewajibkan setiap pihak di atas memproses data Anda sebatas keperluan yang kami tentukan dan menjaga kerahasiaannya.</p>

    <h2 id="retensi">8. Masa Penyimpanan</h2>
    <p>Kami menyimpan data pribadi hanya selama tujuannya masih berjalan atau selama diwajibkan peraturan:</p>
    <ul>
        <li><strong>Pesan dari formulir hubungi kami</strong> — disimpan selama diperlukan untuk menindaklanjuti dan menjadi rujukan bila percakapan berlanjut.</li>
        <li><strong>Data akun</strong> — disimpan selama akun Anda aktif. Bila Anda meminta penghapusan, kami hapus kecuali ada bagian yang wajib kami simpan menurut peraturan.</li>
        <li><strong>Data pemesanan dan transaksi</strong> — disimpan sesuai kewajiban pembukuan dan perpajakan yang berlaku bagi perseroan di Indonesia.</li>
        <li><strong>Data peserta dan catatan sertifikasi</strong> — disimpan selama sertifikat masih berlaku dan sepanjang dibutuhkan untuk verifikasi keabsahannya.</li>
    </ul>
    <p>Data yang sudah habis masa simpannya kami hapus atau kami buat tidak lagi dapat dikaitkan dengan orang tertentu.</p>

    <h2 id="keamanan">9. Keamanan Data</h2>
    <p>Kami menerapkan langkah teknis dan organisasi yang wajar untuk melindungi data Anda, antara lain sambungan terenkripsi, penyimpanan kata sandi dalam bentuk teracak, pembatasan akses hanya kepada personel yang memerlukannya, serta pencatatan aktivitas pada panel pengelolaan.</p>
    <p>Tidak ada sistem elektronik yang sepenuhnya kebal. Apabila terjadi kegagalan pelindungan data pribadi, kami akan memberitahukannya kepada Anda dan kepada lembaga yang berwenang <strong>paling lambat 3 x 24 jam</strong> sejak kami mengetahuinya, sebagaimana diatur Undang-Undang Pelindungan Data Pribadi, disertai keterangan data yang terdampak dan langkah penanganannya.</p>

    <h2 id="hak-anda">10. Hak Anda</h2>
    <p>Sebagai subjek data pribadi, Undang-Undang Nomor 27 Tahun 2022 memberi Anda hak untuk:</p>
    <ul>
        <li>memperoleh informasi tentang identitas kami, dasar hukum, tujuan, dan akuntabilitas pemrosesan data Anda;</li>
        <li><strong>mengakses</strong> data pribadi Anda yang kami simpan dan memperoleh salinannya;</li>
        <li><strong>memperbaiki atau melengkapi</strong> data pribadi Anda yang keliru atau tidak lengkap;</li>
        <li><strong>mengakhiri pemrosesan, menghapus, atau memusnahkan</strong> data pribadi Anda;</li>
        <li><strong>menarik kembali persetujuan</strong> yang pernah Anda berikan;</li>
        <li><strong>menunda atau membatasi</strong> pemrosesan data pribadi Anda;</li>
        <li><strong>mengajukan keberatan</strong> atas pengambilan keputusan yang didasarkan semata pada pemrosesan otomatis;</li>
        <li><strong>memperoleh dan memindahkan</strong> data pribadi Anda kepada pengendali lain, sepanjang sistemnya memungkinkan;</li>
        <li><strong>menuntut dan menerima ganti rugi</strong> atas pelanggaran pemrosesan data pribadi Anda.</li>
    </ul>
    <p>Menarik persetujuan tidak membatalkan keabsahan pemrosesan yang sudah terjadi sebelumnya, dan sebagian data mungkin tetap kami simpan apabila peraturan mewajibkannya.</p>

    <h2 id="cara-hak">11. Cara Menggunakan Hak Anda</h2>
    <p>Kirimkan permintaan Anda ke <a href="mailto:{{ $email }}">{{ $email }}</a> dengan menyebutkan hak yang ingin Anda gunakan dan data yang dimaksud. Untuk melindungi data Anda sendiri, kami akan memastikan terlebih dahulu bahwa permintaan itu benar datang dari Anda.</p>
    <p>Permintaan Anda kami tindak lanjuti dalam waktu yang wajar dan kami beri tahu hasilnya. Apabila permintaan tidak dapat kami penuhi seluruhnya, kami sampaikan alasannya. Anda juga berhak menyampaikan pengaduan kepada lembaga yang berwenang di bidang pelindungan data pribadi.</p>

    <h2 id="anak">12. Data Pribadi Anak</h2>
    <p>Layanan kami ditujukan bagi pengguna dewasa, yaitu yang telah berusia 18 tahun atau sudah menikah. Kami tidak dengan sengaja mengumpulkan data pribadi anak. Pemrosesan data pribadi anak hanya dapat dilakukan dengan persetujuan orang tua atau walinya. Apabila Anda mengetahui ada data anak yang terkirim kepada kami tanpa persetujuan tersebut, mohon hubungi kami agar segera kami hapus.</p>

    <h2 id="perubahan">13. Perubahan Kebijakan</h2>
    <p>Kebijakan ini dapat kami perbarui sewaktu-waktu, misalnya karena perubahan layanan atau peraturan. Versi terbaru selalu tersedia di halaman ini dengan tanggal berlaku yang diperbarui. Untuk perubahan yang berdampak besar bagi hak Anda, kami akan memberitahukannya melalui situs ini atau surel.</p>

    <h2 id="kontak">14. Hubungi Kami</h2>
    <p>Pertanyaan, permintaan, atau keberatan mengenai kebijakan ini dapat Anda sampaikan melalui:</p>
    <ul>
        <li>Surel: <a href="mailto:{{ $email }}">{{ $email }}</a></li>
        @foreach ($offices as $o)
            @if (! empty($o['phone']))
                <li>{{ $o['name'] }}: {{ $o['phone'] }}</li>
            @endif
        @endforeach
        <li>Formulir: <a href="{{ route('contact.index') }}">halaman Kontak</a></li>
    </ul>

@else

    <h2 id="ruang-lingkup">1. Scope</h2>
    <p>This policy applies to all personal data we process through <strong>deltatigaenam.com</strong> and its sub-pages, including the contact form, account registration, training and certification orders, and participant registration.</p>
    <p>It does not cover third-party sites or services linked from ours. Once you leave this site, that party's privacy policy applies.</p>
    <p>Legal basis we follow:</p>
    <ul>
        <li>Law Number 27 of 2022 on Personal Data Protection;</li>
        <li>Law Number 11 of 2008 on Electronic Information and Transactions, as amended by Law Number 19 of 2016;</li>
        <li>Government Regulation Number 71 of 2019 on Electronic System and Transaction Operation.</li>
    </ul>

    <h2 id="pengendali">2. Data Controller</h2>
    <p>The data controller for the processing described here is:</p>
    <p><strong>PT Delta Tiga Enam</strong></p>
    <ul>
        @foreach ($offices as $o)
            <li><strong>{{ $o['name'] }}</strong> — {{ $o['address'] }}@if (! empty($o['phone'])). Phone: {{ $o['phone'] }}@endif</li>
        @endforeach
        <li>Email: <a href="mailto:{{ $email }}">{{ $email }}</a></li>
    </ul>

    <h2 id="data">3. Data We Collect</h2>

    <h3>3.1 Data you provide</h3>
    <p>We only receive this if you enter it. No field is filled in silently.</p>
    <ul>
        <li><strong>Contact form:</strong> name, email address, phone number, subject, and message.</li>
        <li><strong>Account registration:</strong> name, email address, password, phone number, company name, and profile photo if you upload one.</li>
        <li><strong>Programme orders:</strong> name, email address, phone number, company name, number of participants, and any notes you write.</li>
        <li><strong>Participant data:</strong> the name and phone number of each participant you register, plus attendance records during the programme.</li>
    </ul>
    <p>Passwords are stored as one-way hashes. We cannot read them, and we will never ask you to disclose one by email, phone, or message.</p>

    <h3>3.2 Data collected automatically</h3>
    <ul>
        <li><strong>Server logs:</strong> IP address, browser type and version, operating system, pages visited, and the date and time of access. These are generated on every visit and used to keep the site secure and to trace technical faults.</li>
        <li><strong>Technical cookies:</strong> see <a href="#cookie">Cookies</a>.</li>
    </ul>

    <h3>3.3 Data we do not collect</h3>
    <p>We do not collect specific personal data as defined by the Personal Data Protection Law — including health data, biometric data, genetic data, criminal records, political views, and religious beliefs. Please do not include such data in message or note fields.</p>

    <h2 id="dasar">4. Lawful Basis</h2>
    <p>Every processing activity rests on one of the following:</p>
    <ul>
        <li><strong>Your consent</strong> — when you voluntarily submit the contact form or register an account.</li>
        <li><strong>Performance of a contract</strong> — when we process your order, run the programme, and issue the certificate you ordered.</li>
        <li><strong>Compliance with a legal obligation</strong> — including tax, bookkeeping, and reporting duties to the competent certification bodies.</li>
        <li><strong>Legitimate interests</strong> — keeping the system secure, preventing misuse, and improving service quality, so far as this does not override your rights and interests.</li>
    </ul>

    <h2 id="tujuan">5. Purposes of Use</h2>
    <p>We use your personal data only to:</p>
    <ul>
        <li>answer the questions and requests you send us;</li>
        <li>create and manage your account on this site;</li>
        <li>process registration, orders, scheduling, and payment;</li>
        <li>deliver training, competency assessment, and certificate issuance, including attendance records;</li>
        <li>send information directly related to the programme you joined, such as changes of schedule or venue;</li>
        <li>meet legal obligations and resolve disputes should any arise;</li>
        <li>keep the site secure and prevent misuse.</li>
    </ul>
    <p><strong>We do not sell your personal data to anyone.</strong> Nor do we use your data for decisions made solely by automated means without human involvement.</p>

    <h2 id="cookie">6. Cookies</h2>
    <p>This site sets only the cookies it needs to work:</p>
    <ul>
        <li><strong>Session cookie</strong> — keeps you signed in as you move between pages, and is cleared when the session ends.</li>
        <li><strong>Security token cookie</strong> — protects forms against cross-site request forgery.</li>
        <li><strong>Language preference</strong> — remembers your choice of Indonesian or English.</li>
    </ul>
    <p>We set <strong>no advertising cookies and no third-party tracking cookies</strong> on this site. You can clear the technical cookies above through your browser settings, but parts of the site — signing in above all — will not work without them.</p>

    <h2 id="pihak-ketiga">7. Sharing with Third Parties</h2>
    <p>We share your personal data only so far as necessary, with:</p>
    <ul>
        <li><strong>Payment service providers</strong> — to process order transactions. Your card or account details are handled directly by that provider and are never stored in our system.</li>
        <li><strong>Certification bodies and assessors</strong> — participant names and data are passed to the issuing body so far as needed to run the assessment and issue the certificate you ordered.</li>
        <li><strong>Infrastructure providers</strong> — the hosting and email services we use to run this site.</li>
        <li><strong>Law enforcement or competent authorities</strong> — where required by law or a binding order.</li>
    </ul>
    <p>We require each of these parties to process your data only for the purposes we set and to keep it confidential.</p>

    <h2 id="retensi">8. Retention Period</h2>
    <p>We keep personal data only while its purpose stands or while the law requires:</p>
    <ul>
        <li><strong>Contact form messages</strong> — kept as long as needed to follow up and for reference if the conversation continues.</li>
        <li><strong>Account data</strong> — kept while your account is active. On a deletion request we remove it, except any part we are legally required to retain.</li>
        <li><strong>Order and transaction data</strong> — kept in line with the bookkeeping and tax obligations applicable to companies in Indonesia.</li>
        <li><strong>Participant and certification records</strong> — kept while the certificate remains valid and as long as needed to verify its authenticity.</li>
    </ul>
    <p>Data past its retention period is deleted or rendered no longer attributable to an identified person.</p>

    <h2 id="keamanan">9. Data Security</h2>
    <p>We apply reasonable technical and organisational measures, including encrypted connections, hashed password storage, access limited to personnel who need it, and activity logging in the management panel.</p>
    <p>No electronic system is wholly immune. Should a personal data protection failure occur, we will notify you and the competent authority <strong>within 3 x 24 hours</strong> of becoming aware of it, as required by the Personal Data Protection Law, stating the data affected and the steps being taken.</p>

    <h2 id="hak-anda">10. Your Rights</h2>
    <p>As a data subject, Law Number 27 of 2022 grants you the right to:</p>
    <ul>
        <li>obtain information on our identity, the lawful basis, purpose, and accountability of the processing;</li>
        <li><strong>access</strong> the personal data we hold about you and obtain a copy;</li>
        <li><strong>correct or complete</strong> personal data that is inaccurate or incomplete;</li>
        <li><strong>end processing, delete, or destroy</strong> your personal data;</li>
        <li><strong>withdraw consent</strong> you previously gave;</li>
        <li><strong>postpone or restrict</strong> processing of your personal data;</li>
        <li><strong>object</strong> to decisions based solely on automated processing;</li>
        <li><strong>obtain and transfer</strong> your personal data to another controller, where the systems allow;</li>
        <li><strong>claim and receive compensation</strong> for a breach in the processing of your personal data.</li>
    </ul>
    <p>Withdrawing consent does not undo processing that already took place lawfully, and some data may still be retained where the law requires it.</p>

    <h2 id="cara-hak">11. Exercising Your Rights</h2>
    <p>Send your request to <a href="mailto:{{ $email }}">{{ $email }}</a>, stating the right you wish to exercise and the data concerned. To protect your own data, we will first confirm the request genuinely came from you.</p>
    <p>We act on requests within a reasonable time and tell you the outcome. Where a request cannot be met in full, we explain why. You also have the right to lodge a complaint with the competent personal data protection authority.</p>

    <h2 id="anak">12. Children's Data</h2>
    <p>Our services are intended for adult users — those aged 18 or over, or already married. We do not knowingly collect children's personal data. Processing a child's personal data requires the consent of a parent or guardian. If you become aware that a child's data has reached us without such consent, please contact us so we can delete it.</p>

    <h2 id="perubahan">13. Changes to This Policy</h2>
    <p>We may update this policy from time to time, for example as services or regulations change. The current version is always on this page with an updated effective date. For changes that materially affect your rights, we will give notice through this site or by email.</p>

    <h2 id="kontak">14. Contact Us</h2>
    <p>Questions, requests, or objections about this policy can be sent to:</p>
    <ul>
        <li>Email: <a href="mailto:{{ $email }}">{{ $email }}</a></li>
        @foreach ($offices as $o)
            @if (! empty($o['phone']))
                <li>{{ $o['name'] }}: {{ $o['phone'] }}</li>
            @endif
        @endforeach
        <li>Form: <a href="{{ route('contact.index') }}">Contact page</a></li>
    </ul>

@endif

</x-legal-page>
