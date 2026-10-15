@php
    $isId = app()->getLocale() === 'id';
    $offices = \App\Support\Company::offices();
    $email = 'info@deltatigaenam.com';

    $toc = $isId ? [
        'penerimaan'   => 'Penerimaan Syarat',
        'definisi'     => 'Definisi',
        'layanan'      => 'Layanan Kami',
        'akun'         => 'Akun Pengguna',
        'pemesanan'    => 'Pemesanan Program',
        'harga'        => 'Harga dan Pembayaran',
        'pembatalan'   => 'Pembatalan dan Penjadwalan Ulang',
        'kewajiban'    => 'Kewajiban Peserta',
        'sertifikat'   => 'Sertifikat dan Kelulusan',
        'kekayaan'     => 'Hak Kekayaan Intelektual',
        'larangan'     => 'Larangan Penggunaan',
        'tautan'       => 'Tautan Pihak Ketiga',
        'tanggung'     => 'Batasan Tanggung Jawab',
        'keadaan'      => 'Keadaan Kahar',
        'pengakhiran'  => 'Pengakhiran',
        'perubahan'    => 'Perubahan Syarat',
        'hukum'        => 'Hukum dan Penyelesaian Sengketa',
        'kontak'       => 'Hubungi Kami',
    ] : [
        'penerimaan'   => 'Acceptance of Terms',
        'definisi'     => 'Definitions',
        'layanan'      => 'Our Services',
        'akun'         => 'User Accounts',
        'pemesanan'    => 'Programme Orders',
        'harga'        => 'Pricing and Payment',
        'pembatalan'   => 'Cancellation and Rescheduling',
        'kewajiban'    => 'Participant Obligations',
        'sertifikat'   => 'Certificates and Completion',
        'kekayaan'     => 'Intellectual Property',
        'larangan'     => 'Prohibited Use',
        'tautan'       => 'Third-Party Links',
        'tanggung'     => 'Limitation of Liability',
        'keadaan'      => 'Force Majeure',
        'pengakhiran'  => 'Termination',
        'perubahan'    => 'Changes to These Terms',
        'hukum'        => 'Governing Law and Disputes',
        'kontak'       => 'Contact Us',
    ];
@endphp

<x-legal-page
    :title="$isId ? 'Syarat & Ketentuan' : 'Terms & Conditions'"
    :subtitle="$isId
        ? 'Ketentuan yang berlaku saat Anda menggunakan situs dan layanan PT Delta Tiga Enam.'
        : 'The terms that apply when you use the site and services of PT Delta Tiga Enam.'"
    :updated="$isId ? '15 Oktober 2026' : '15 October 2026'"
    :intro="$isId
        ? 'Syarat dan ketentuan ini mengatur hubungan antara Anda dan PT Delta Tiga Enam saat Anda mengakses situs ini, mendaftarkan akun, atau memesan program pelatihan, sertifikasi, dan layanan lain yang kami sediakan. Mohon dibaca sebelum Anda menggunakan layanan kami.'
        : 'These terms govern the relationship between you and PT Delta Tiga Enam when you access this site, register an account, or order training, certification, and other services we provide. Please read them before using our services.'"
    :toc="$toc">

@if ($isId)

    <h2 id="penerimaan">1. Penerimaan Syarat</h2>
    <p>Dengan mengakses situs ini, mendaftarkan akun, atau memesan layanan kami, Anda menyatakan telah membaca, memahami, dan menyetujui seluruh ketentuan di halaman ini. Apabila Anda tidak menyetujuinya, mohon tidak menggunakan situs dan layanan kami.</p>
    <p>Apabila Anda bertindak untuk dan atas nama suatu badan usaha, Anda menyatakan berwenang mengikat badan usaha tersebut pada ketentuan ini.</p>

    <h2 id="definisi">2. Definisi</h2>
    <ul>
        <li><strong>&ldquo;Kami&rdquo;</strong> berarti PT Delta Tiga Enam, perseroan terbatas yang didirikan menurut hukum Republik Indonesia.</li>
        <li><strong>&ldquo;Situs&rdquo;</strong> berarti deltatigaenam.com beserta seluruh subhalamannya.</li>
        <li><strong>&ldquo;Anda&rdquo;</strong> atau <strong>&ldquo;Pengguna&rdquo;</strong> berarti setiap orang atau badan usaha yang mengakses Situs atau menggunakan layanan kami.</li>
        <li><strong>&ldquo;Program&rdquo;</strong> berarti pelatihan, uji kompetensi, sertifikasi, konsultasi, atau layanan lain yang kami selenggarakan.</li>
        <li><strong>&ldquo;Peserta&rdquo;</strong> berarti orang yang terdaftar mengikuti Program, baik mendaftar sendiri maupun didaftarkan oleh perusahaannya.</li>
    </ul>

    <h2 id="layanan">3. Layanan Kami</h2>
    <p>Kami menyediakan layanan pengembangan sumber daya manusia yang mencakup pelatihan, uji kompetensi dan sertifikasi profesi, konsultasi human capital, serta pencarian dan penempatan talenta.</p>
    <p>Keterangan Program di Situs (termasuk silabus, durasi, jadwal, dan tempat pelaksanaan) kami susun dengan cermat, namun dapat berubah sewaktu-waktu. Perubahan yang memengaruhi Program yang sudah Anda pesan akan kami beritahukan sedini mungkin.</p>
    <p>Penyelenggaraan sebagian Program bergantung pada tercapainya jumlah peserta minimum. Apabila jumlah itu tidak tercapai, kami berhak menjadwalkan ulang atau membatalkan Program dengan pemberitahuan kepada Anda.</p>

    <h2 id="akun">4. Akun Pengguna</h2>
    <p>Sebagian layanan mensyaratkan Anda memiliki akun. Dengan membuat akun, Anda menyatakan bahwa:</p>
    <ul>
        <li>data yang Anda berikan benar, akurat, dan Anda perbarui bila berubah;</li>
        <li>Anda berusia sekurang-kurangnya 18 tahun atau sudah menikah;</li>
        <li>Anda bertanggung jawab menjaga kerahasiaan kata sandi Anda;</li>
        <li>Anda bertanggung jawab atas seluruh aktivitas yang terjadi melalui akun Anda.</li>
    </ul>
    <p>Segera beri tahu kami apabila Anda menduga akun Anda digunakan pihak lain tanpa izin. Kami berhak menangguhkan atau menutup akun yang kami nilai digunakan secara melanggar hukum atau ketentuan ini.</p>

    <h2 id="pemesanan">5. Pemesanan Program</h2>
    <p>Pemesanan yang Anda kirim melalui Situs merupakan penawaran untuk mengikuti Program. Perjanjian baru terbentuk setelah kami menerbitkan konfirmasi pemesanan. Kami berhak menolak atau membatalkan pemesanan, antara lain apabila kuota telah penuh, data yang diberikan tidak lengkap, atau terdapat dugaan penyalahgunaan.</p>
    <p>Apabila Anda mendaftarkan orang lain sebagai Peserta, Anda menyatakan telah memperoleh persetujuan mereka untuk menyerahkan data pribadinya kepada kami sebagaimana dijelaskan dalam <a href="{{ route('privacy') }}">Kebijakan Privasi</a>.</p>

    <h2 id="harga">6. Harga dan Pembayaran</h2>
    <p>Harga yang tercantum di Situs dinyatakan dalam Rupiah. Keterangan mengenai pajak dan komponen biaya lain disampaikan pada rincian pemesanan sebelum Anda menyelesaikan transaksi.</p>
    <p>Pembayaran diproses melalui penyedia layanan pembayaran pihak ketiga. Data instrumen pembayaran Anda diproses langsung oleh penyedia tersebut dan tidak tersimpan di sistem kami.</p>
    <p>Kami berhak mengubah harga sewaktu-waktu. Perubahan harga tidak berlaku surut terhadap pemesanan yang telah kami konfirmasi.</p>
    <p>Tempat Peserta pada suatu Program baru dianggap terkunci setelah pembayaran kami terima sesuai tenggat yang tercantum pada konfirmasi pemesanan.</p>

    <h2 id="pembatalan">7. Pembatalan dan Penjadwalan Ulang</h2>
    <p>Ketentuan pembatalan, penjadwalan ulang, penggantian Peserta, dan pengembalian dana untuk setiap Program tercantum pada konfirmasi pemesanan atau perjanjian tertulis yang kami sepakati bersama Anda. Ketentuan itulah yang berlaku bagi Program yang bersangkutan.</p>
    <p>Permohonan pembatalan atau penjadwalan ulang dari pihak Anda disampaikan secara tertulis melalui surel ke <a href="mailto:{{ $email }}">{{ $email }}</a>.</p>
    <p>Apabila Program dibatalkan oleh kami dan bukan karena kesalahan Anda, Anda berhak memilih antara mengikuti jadwal pengganti atau menerima pengembalian dana atas biaya yang telah Anda bayarkan untuk Program tersebut.</p>

    <h2 id="kewajiban">8. Kewajiban Peserta</h2>
    <p>Selama mengikuti Program, Peserta wajib:</p>
    <ul>
        <li>memberikan data diri yang benar dan dokumen pendukung yang sah;</li>
        <li>memenuhi persyaratan Program, termasuk kehadiran minimum apabila disyaratkan;</li>
        <li>mengikuti tata tertib pelaksanaan dan ketentuan keselamatan di tempat Program;</li>
        <li>tidak melakukan kecurangan dalam bentuk apa pun pada saat uji kompetensi.</li>
    </ul>
    <p>Pelanggaran atas ketentuan ini dapat berakibat pada pembatalan keikutsertaan tanpa pengembalian dana, dan pembatalan hasil uji kompetensi apabila kecurangan baru diketahui kemudian.</p>

    <h2 id="sertifikat">9. Sertifikat dan Kelulusan</h2>
    <p><strong>Mengikuti Program tidak dengan sendirinya berarti lulus.</strong> Sertifikat kompetensi diterbitkan hanya apabila Peserta memenuhi kriteria kelulusan yang ditetapkan skema dan lembaga sertifikasi yang berwenang. Keputusan kelulusan berada pada asesor dan lembaga penerbit, bukan pada kami.</p>
    <p>Masa berlaku, ruang lingkup, dan ketentuan perpanjangan sertifikat mengikuti aturan lembaga penerbitnya.</p>
    <p>Sertifikat yang kami terbitkan atau fasilitasi tidak boleh diubah, digandakan untuk tujuan yang menyesatkan, atau digunakan oleh orang selain pemegangnya. Pelanggaran atas hal ini dapat berakibat pada pembatalan sertifikat dan tindakan hukum.</p>

    <h2 id="kekayaan">10. Hak Kekayaan Intelektual</h2>
    <p>Seluruh isi Situs (termasuk teks, logo, lambang, tata letak, foto, dan materi pelatihan) dilindungi hak cipta dan merek, dan merupakan milik kami atau pihak yang memberi kami lisensi.</p>
    <p>Materi pelatihan yang Anda terima diberikan untuk keperluan belajar Anda sendiri. Tanpa izin tertulis dari kami, materi itu tidak boleh digandakan, disebarluaskan, dijual, diterjemahkan, atau digunakan untuk menyelenggarakan pelatihan oleh pihak lain.</p>
    <p>Perekaman suara maupun gambar selama Program berlangsung hanya diperkenankan dengan izin kami terlebih dahulu.</p>

    <h2 id="larangan">11. Larangan Penggunaan</h2>
    <p>Anda dilarang:</p>
    <ul>
        <li>menggunakan Situs untuk tujuan yang melanggar hukum atau merugikan pihak lain;</li>
        <li>mencoba memperoleh akses tidak sah ke sistem, akun pengguna lain, atau data yang bukan milik Anda;</li>
        <li>mengganggu atau membebani Situs secara tidak wajar, termasuk melalui program otomatis;</li>
        <li>mengunggah atau mengirimkan perangkat lunak berbahaya;</li>
        <li>mengambil isi Situs secara massal untuk digunakan kembali tanpa izin tertulis kami;</li>
        <li>memalsukan identitas atau menyamar sebagai orang atau badan lain.</li>
    </ul>

    <h2 id="tautan">12. Tautan Pihak Ketiga</h2>
    <p>Situs ini dapat memuat tautan ke situs pihak lain. Tautan tersebut kami sediakan untuk memudahkan Anda, dan tidak berarti kami mengendalikan atau menjamin isinya. Kami tidak bertanggung jawab atas isi, kebijakan, maupun praktik situs pihak lain.</p>

    <h2 id="tanggung">13. Batasan Tanggung Jawab</h2>
    <p>Kami berupaya menjaga Situs tetap tersedia dan informasinya mutakhir, namun tidak menjamin Situs bebas dari gangguan, kesalahan, atau penghentian sementara karena pemeliharaan maupun sebab teknis di luar kendali kami.</p>
    <p>Sepanjang diperkenankan peraturan perundang-undangan, tanggung jawab kami yang timbul dari atau berkaitan dengan suatu Program dibatasi setinggi-tingginya pada jumlah yang telah Anda bayarkan untuk Program tersebut.</p>
    <p>Pembatasan ini tidak mengurangi hak Anda selaku konsumen menurut Undang-Undang Nomor 8 Tahun 1999 tentang Perlindungan Konsumen, dan tidak berlaku terhadap kerugian yang timbul dari kesengajaan atau kelalaian berat kami.</p>

    <h2 id="keadaan">14. Keadaan Kahar</h2>
    <p>Kami tidak dapat dimintai pertanggungjawaban atas keterlambatan atau kegagalan memenuhi kewajiban yang disebabkan keadaan di luar kendali wajar kami, antara lain bencana alam, wabah, kebakaran, kerusuhan, perang, pemogokan, gangguan jaringan listrik atau telekomunikasi berskala luas, serta kebijakan pemerintah yang menghalangi pelaksanaan Program.</p>
    <p>Dalam keadaan demikian, kami akan berunding dengan Anda mengenai penjadwalan ulang atau penyelesaian lain yang wajar.</p>

    <h2 id="pengakhiran">15. Pengakhiran</h2>
    <p>Kami berhak menangguhkan atau mengakhiri akses Anda ke Situs dan layanan kami apabila Anda melanggar ketentuan ini, dengan pemberitahuan sepanjang keadaan memungkinkan.</p>
    <p>Anda dapat menutup akun Anda sewaktu-waktu dengan menghubungi kami. Pengakhiran tidak menghapus kewajiban yang telah timbul sebelumnya, termasuk kewajiban pembayaran yang belum diselesaikan.</p>

    <h2 id="perubahan">16. Perubahan Syarat</h2>
    <p>Ketentuan ini dapat kami perbarui sewaktu-waktu. Versi terbaru selalu tersedia di halaman ini dengan tanggal berlaku yang diperbarui. Penggunaan Situs setelah perubahan berlaku berarti Anda menerima ketentuan yang telah diperbarui. Untuk pemesanan yang telah dikonfirmasi, ketentuan yang berlaku adalah yang berlaku pada saat konfirmasi diterbitkan.</p>

    <h2 id="hukum">17. Hukum dan Penyelesaian Sengketa</h2>
    <p>Ketentuan ini tunduk pada dan ditafsirkan menurut hukum Republik Indonesia.</p>
    <p>Setiap perselisihan yang timbul akan kami upayakan diselesaikan terlebih dahulu secara musyawarah untuk mufakat dalam waktu yang wajar sejak pemberitahuan tertulis disampaikan salah satu pihak. Apabila musyawarah tidak mencapai kesepakatan, perselisihan diselesaikan melalui Pengadilan Negeri yang berwenang di wilayah hukum Republik Indonesia.</p>
    <p>Ketentuan ini tidak mengurangi hak Anda selaku konsumen untuk menempuh penyelesaian melalui Badan Penyelesaian Sengketa Konsumen atau lembaga lain yang berwenang menurut peraturan perundang-undangan.</p>

    <h2 id="kontak">18. Hubungi Kami</h2>
    <p>Pertanyaan mengenai syarat dan ketentuan ini dapat Anda sampaikan melalui:</p>
    <ul>
        <li>Surel: <a href="mailto:{{ $email }}">{{ $email }}</a></li>
        @foreach ($offices as $o)
            <li>{{ $o['name'] }}: {{ $o['address'] }}@if (! empty($o['phone'])) · {{ $o['phone'] }}@endif</li>
        @endforeach
        <li>Formulir: <a href="{{ route('contact.index') }}">halaman Kontak</a></li>
    </ul>

@else

    <h2 id="penerimaan">1. Acceptance of Terms</h2>
    <p>By accessing this site, registering an account, or ordering our services, you confirm that you have read, understood, and agreed to everything on this page. If you do not agree, please do not use our site or services.</p>
    <p>If you act for and on behalf of a business entity, you confirm that you are authorised to bind that entity to these terms.</p>

    <h2 id="definisi">2. Definitions</h2>
    <ul>
        <li><strong>&ldquo;We&rdquo;</strong> means PT Delta Tiga Enam, a limited liability company established under the laws of the Republic of Indonesia.</li>
        <li><strong>&ldquo;Site&rdquo;</strong> means deltatigaenam.com and all its sub-pages.</li>
        <li><strong>&ldquo;You&rdquo;</strong> or <strong>&ldquo;User&rdquo;</strong> means any person or entity accessing the Site or using our services.</li>
        <li><strong>&ldquo;Programme&rdquo;</strong> means training, competency assessment, certification, consultancy, or other services we deliver.</li>
        <li><strong>&ldquo;Participant&rdquo;</strong> means a person enrolled in a Programme, whether self-enrolled or registered by their employer.</li>
    </ul>

    <h2 id="layanan">3. Our Services</h2>
    <p>We provide human resource development services covering training, competency assessment and professional certification, human capital consultancy, and talent search and placement.</p>
    <p>Programme details on the Site (syllabus, duration, schedule, and venue) are prepared carefully but may change. Changes affecting a Programme you have ordered will be notified as early as possible.</p>
    <p>Some Programmes run only if a minimum number of participants is reached. If that number is not met, we may reschedule or cancel the Programme with notice to you.</p>

    <h2 id="akun">4. User Accounts</h2>
    <p>Some services require an account. By creating one, you confirm that:</p>
    <ul>
        <li>the data you provide is true and accurate, and you will update it if it changes;</li>
        <li>you are at least 18 years old or already married;</li>
        <li>you are responsible for keeping your password confidential;</li>
        <li>you are responsible for all activity carried out through your account.</li>
    </ul>
    <p>Tell us promptly if you suspect your account is being used without your permission. We may suspend or close an account we consider to be used unlawfully or in breach of these terms.</p>

    <h2 id="pemesanan">5. Programme Orders</h2>
    <p>An order you submit through the Site is an offer to join a Programme. The agreement forms once we issue an order confirmation. We may decline or cancel an order, for instance where the quota is full, the data given is incomplete, or misuse is suspected.</p>
    <p>If you register other people as Participants, you confirm you have their consent to pass their personal data to us as described in our <a href="{{ route('privacy') }}">Privacy Policy</a>.</p>

    <h2 id="harga">6. Pricing and Payment</h2>
    <p>Prices shown on the Site are in Indonesian Rupiah. Tax and any other cost components are stated in the order summary before you complete the transaction.</p>
    <p>Payments are processed through a third-party payment provider. Your payment instrument details are handled directly by that provider and are not stored in our system.</p>
    <p>We may change prices at any time. Price changes do not apply retroactively to orders we have already confirmed.</p>
    <p>A Participant's place on a Programme is secured only once payment is received by the deadline stated in the order confirmation.</p>

    <h2 id="pembatalan">7. Cancellation and Rescheduling</h2>
    <p>The terms for cancellation, rescheduling, participant substitution, and refunds for each Programme are set out in the order confirmation or the written agreement made with you. Those terms govern the Programme concerned.</p>
    <p>Requests to cancel or reschedule should be sent in writing by email to <a href="mailto:{{ $email }}">{{ $email }}</a>.</p>
    <p>If we cancel a Programme through no fault of yours, you may choose between a replacement date or a refund of the fees you paid for that Programme.</p>

    <h2 id="kewajiban">8. Participant Obligations</h2>
    <p>While taking part in a Programme, Participants must:</p>
    <ul>
        <li>provide accurate personal details and valid supporting documents;</li>
        <li>meet the Programme requirements, including minimum attendance where required;</li>
        <li>follow the rules of conduct and safety requirements at the venue;</li>
        <li>not cheat in any form during competency assessment.</li>
    </ul>
    <p>Breaching these may result in removal from the Programme without refund, and cancellation of assessment results if cheating comes to light later.</p>

    <h2 id="sertifikat">9. Certificates and Completion</h2>
    <p><strong>Attending a Programme does not by itself mean passing it.</strong> A competency certificate is issued only where the Participant meets the pass criteria set by the scheme and the competent certification body. The pass decision rests with the assessor and the issuing body, not with us.</p>
    <p>Validity, scope, and renewal of a certificate follow the rules of the issuing body.</p>
    <p>Certificates we issue or facilitate must not be altered, reproduced for misleading purposes, or used by anyone other than the holder. Breaching this may lead to cancellation of the certificate and legal action.</p>

    <h2 id="kekayaan">10. Intellectual Property</h2>
    <p>All content on the Site (text, logos, marks, layout, photographs, and training material) is protected by copyright and trade mark law, and belongs to us or to those who license it to us.</p>
    <p>Training material you receive is provided for your own learning. Without our written permission it may not be reproduced, distributed, sold, translated, or used to deliver training by anyone else.</p>
    <p>Audio or video recording during a Programme is permitted only with our prior consent.</p>

    <h2 id="larangan">11. Prohibited Use</h2>
    <p>You must not:</p>
    <ul>
        <li>use the Site for unlawful purposes or to harm others;</li>
        <li>attempt to gain unauthorised access to the system, other users' accounts, or data that is not yours;</li>
        <li>disrupt or unreasonably burden the Site, including by automated means;</li>
        <li>upload or transmit malicious software;</li>
        <li>extract Site content in bulk for reuse without our written permission;</li>
        <li>falsify your identity or impersonate another person or entity.</li>
    </ul>

    <h2 id="tautan">12. Third-Party Links</h2>
    <p>This Site may link to other sites. Such links are provided for your convenience and do not mean we control or vouch for their content. We are not responsible for the content, policies, or practices of other sites.</p>

    <h2 id="tanggung">13. Limitation of Liability</h2>
    <p>We work to keep the Site available and its information current, but do not warrant that it will be free from interruption, error, or temporary suspension for maintenance or technical reasons beyond our control.</p>
    <p>To the extent permitted by law, our liability arising from or in connection with a Programme is limited to the amount you paid for that Programme.</p>
    <p>This limitation does not reduce your rights as a consumer under Law Number 8 of 1999 on Consumer Protection, and does not apply to loss arising from our wilful misconduct or gross negligence.</p>

    <h2 id="keadaan">14. Force Majeure</h2>
    <p>We are not liable for delay or failure to perform caused by circumstances beyond our reasonable control, including natural disaster, epidemic, fire, civil unrest, war, strikes, widespread power or telecommunications failure, and government measures preventing a Programme from running.</p>
    <p>In such circumstances we will discuss rescheduling or another reasonable resolution with you.</p>

    <h2 id="pengakhiran">15. Termination</h2>
    <p>We may suspend or end your access to the Site and our services if you breach these terms, with notice where circumstances allow.</p>
    <p>You may close your account at any time by contacting us. Termination does not extinguish obligations already incurred, including unpaid amounts.</p>

    <h2 id="perubahan">16. Changes to These Terms</h2>
    <p>We may update these terms from time to time. The current version is always on this page with an updated effective date. Using the Site after a change takes effect means you accept the updated terms. For orders already confirmed, the terms in force when the confirmation was issued apply.</p>

    <h2 id="hukum">17. Governing Law and Disputes</h2>
    <p>These terms are governed by and construed under the laws of the Republic of Indonesia.</p>
    <p>Any dispute will first be addressed through good-faith discussion within a reasonable period after one party gives written notice. If discussion does not reach agreement, the dispute will be settled through the competent District Court within the jurisdiction of the Republic of Indonesia.</p>
    <p>This does not reduce your right as a consumer to seek resolution through the Consumer Dispute Settlement Body or another competent authority under applicable law.</p>

    <h2 id="kontak">18. Contact Us</h2>
    <p>Questions about these terms can be sent to:</p>
    <ul>
        <li>Email: <a href="mailto:{{ $email }}">{{ $email }}</a></li>
        @foreach ($offices as $o)
            <li>{{ $o['name'] }}: {{ $o['address'] }}@if (! empty($o['phone'])) · {{ $o['phone'] }}@endif</li>
        @endforeach
        <li>Form: <a href="{{ route('contact.index') }}">Contact page</a></li>
    </ul>

@endif

</x-legal-page>
