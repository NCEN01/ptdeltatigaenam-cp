<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Anchor tujuan setelah mencari atau berpindah halaman.
     *
     * Pagination dan form pencarian memuat ulang halaman penuh, jadi browser
     * selalu mendarat di puncak dokumen dan pembaca kehilangan tempatnya.
     * Paginator memakai ->fragment(self::RESULTS_ANCHOR) dan seksi hasil di
     * view memakai id yang sama, dibaca dari konstanta ini supaya keduanya
     * tidak bisa berbeda. Kalau berbeda, fragmennya diam-diam tidak menuju ke
     * mana pun dan tidak ada galat apa pun yang muncul.
     */
    public const RESULTS_ANCHOR = 'hasil';
}
