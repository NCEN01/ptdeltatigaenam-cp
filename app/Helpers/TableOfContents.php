<?php

namespace App\Helpers;

use DOMDocument;
use DOMElement;
use DOMNode;
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
     * Editors using the rich text editor routinely mark section titles as a
     * bold paragraph rather than using the heading control, and paste bodies
     * that open with their own <h1>. Both are treated as real section headings
     * here so the contents panel reflects how the article actually reads.
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

        // Promote "the whole paragraph is bold" into a real heading.
        foreach (iterator_to_array($xpath->query('//p')) as $paragraph) {
            $bold = self::soleBoldChild($paragraph);

            if (! $bold) {
                continue;
            }

            $text = trim($paragraph->textContent);

            if ($text === '' || mb_strlen($text) > self::MAX_PROMOTED_HEADING_LENGTH) {
                continue;
            }

            // Drop the <strong> wrapper — a heading is already emphatic.
            $heading = $dom->createElement('h2');
            while ($bold->firstChild) {
                $heading->appendChild($bold->firstChild);
            }
            $paragraph->parentNode->replaceChild($heading, $paragraph);
        }

        $items = [];
        $used = [];

        foreach ($xpath->query('//h2 | //h3') as $heading) {
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

    /**
     * The single <strong>/<b> that makes up the entire paragraph, or null when
     * the paragraph mixes bold with other content (i.e. it is ordinary prose).
     */
    private static function soleBoldChild(DOMNode $paragraph): ?DOMElement
    {
        $bold = null;

        foreach ($paragraph->childNodes as $child) {
            if ($child instanceof DOMElement) {
                // A second element, or a non-bold one, means this is prose.
                if ($bold !== null || ! in_array(strtolower($child->nodeName), ['strong', 'b'], true)) {
                    return null;
                }

                $bold = $child;

                continue;
            }

            // Any visible text outside the bold run disqualifies the paragraph.
            if (trim($child->textContent) !== '') {
                return null;
            }
        }

        return $bold;
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
