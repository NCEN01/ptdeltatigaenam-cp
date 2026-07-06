<?php

namespace App\Helpers;

use DOMDocument;
use DOMXPath;

class HtmlSanitizer
{
    /**
     * Sanitize HTML content by stripping dangerous tags and attributes.
     */
    public static function clean(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        // Prevent HTML entity encoding issues with UTF-8
        $html = mb_encode_numericentity($html, [0x80, 0x10ffff, 0, 0x1fffff], 'UTF-8');

        $dom = new DOMDocument();
        // Suppress errors due to HTML5 tags
        libxml_use_internal_errors(true);
        $dom->loadHTML('<div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // 1. Remove dangerous tags
        $dangerousTags = ['script', 'style', 'iframe', 'object', 'embed', 'applet', 'frameset', 'frame', 'meta', 'link', 'base'];
        foreach ($dangerousTags as $tag) {
            $nodes = $xpath->query('//' . $tag);
            foreach ($nodes as $node) {
                if ($node->parentNode) {
                    $node->parentNode->removeChild($node);
                }
            }
        }

        // 2. Remove dangerous attributes (on* event handlers, javascript: URIs)
        $allNodes = $xpath->query('//*');
        foreach ($allNodes as $node) {
            $attributesToRemove = [];
            foreach ($node->attributes as $attr) {
                $name = strtolower($attr->name);
                $value = strtolower($attr->value);

                // Strip any "on*" event listener attribute
                if (str_starts_with($name, 'on')) {
                    $attributesToRemove[] = $attr->name;
                }
                // Strip "javascript:", "data:", or "vbscript:" links/sources
                elseif (($name === 'href' || $name === 'src' || $name === 'action') && 
                        (str_starts_with(trim($value), 'javascript:') || 
                         str_starts_with(trim($value), 'data:') || 
                         str_starts_with(trim($value), 'vbscript:'))) {
                    $attributesToRemove[] = $attr->name;
                }
            }

            foreach ($attributesToRemove as $attrName) {
                $node->removeAttribute($attrName);
            }
        }

        // Get inner HTML of the wrapper div
        $wrapper = $dom->getElementsByTagName('div')->item(0);
        $cleanHtml = '';
        if ($wrapper) {
            foreach ($wrapper->childNodes as $child) {
                $cleanHtml .= $dom->saveHTML($child);
            }
        }

        return mb_decode_numericentity($cleanHtml, [0x80, 0x10ffff, 0, 0x1fffff], 'UTF-8');
    }
}
