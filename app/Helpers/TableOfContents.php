<?php

namespace App\Helpers;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Str;

class TableOfContents
{
    /**
     * A bold line longer than this is prose that happens to be emphasised,
     * not a section title, so it is left alone.
     */
    private const MAX_PROMOTED_HEADING_LENGTH = 140;

    /**
     * Normalise an article body for display and return the heading list that
     * drives the table of contents.
     *
     * Daftar isi dibaca dari Heading 2 saja (petunjuk yang sama tertulis di
     * kolom Konten di panel admin). Paragraf yang seluruhnya tebal dulu ikut
     * dinaikkan menjadi heading; akibatnya pertanyaan FAQ dan kalimat ajakan
     * di akhir artikel ikut masuk daftar isi dan tampil sebesar judul bagian.
     * Kini ia dibiarkan sebagai paragraf tebal, persis seperti ditulis.
     * <h1> di dalam isi tetap diturunkan menjadi <h2>: judul halaman sudah h1.
     *
     * Expects already-sanitized HTML (see HtmlSanitizer::clean).
     *
     * @return array{html: string, items: array<int, array{id: string, text: string, level: int}>}
     */
    public static function build(?string $html): array
    {
        if (blank($html)) {
            return ['html' => '', 'items' => []];
        }

        // Keep multibyte characters intact through DOMDocument (same trick as HtmlSanitizer).
        $encoded = mb_encode_numericentity($html, [0x80, 0x10ffff, 0, 0x1fffff], 'UTF-8');

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<div>'.$encoded.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // The page title is the document's h1, so an h1 inside the body is a
        // section heading in the wrong slot — demote it rather than ship two h1s.
        foreach (iterator_to_array($xpath->query('//h1')) as $h1) {
            self::retag($h1, 'h2');
        }

        $items = [];
        $used = [];

        // Hanya Heading 2. Heading 3 adalah sub-bagian di dalam satu butir,
        // dan memasukkannya membuat daftar isi artikel panjang dua kali lipat
        // tanpa menambah arah baru bagi pembaca.
        foreach ($xpath->query('//h2') as $heading) {
            if (! $heading instanceof DOMElement) {
                continue;
            }

            $text = trim(preg_replace('/\s+/u', ' ', $heading->textContent) ?? '');

            if ($text === '') {
                continue;
            }

            $base = Str::slug($text) ?: 'bagian';
            $slug = $base;
            $suffix = 2;

            while (isset($used[$slug])) {
                $slug = $base.'-'.$suffix++;
            }

            $used[$slug] = true;
            $heading->setAttribute('id', $slug);

            $items[] = [
                'id' => $slug,
                'text' => $text,
                'level' => (int) substr($heading->nodeName, 1),
            ];
        }

        $wrapper = $dom->getElementsByTagName('div')->item(0);
        $out = '';

        if ($wrapper) {
            foreach ($wrapper->childNodes as $child) {
                $out .= $dom->saveHTML($child);
            }
        }

        return [
            'html' => mb_decode_numericentity($out, [0x80, 0x10ffff, 0, 0x1fffff], 'UTF-8'),
            'items' => $items,
        ];
    }

    /** Replace an element with the same children under a different tag name. */
    private static function retag(DOMElement $element, string $tag): void
    {
        $replacement = $element->ownerDocument->createElement($tag);

        while ($element->firstChild) {
            $replacement->appendChild($element->firstChild);
        }

        $element->parentNode?->replaceChild($replacement, $element);
    }
}
