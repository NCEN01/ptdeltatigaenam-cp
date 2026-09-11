@props([
    'url' => null,
    'title' => '',
])

@php
    $shareUrl = $url ?: url()->current();
    $isId = app()->getLocale() === 'id';

    $e = fn ($v) => rawurlencode($v);

    // Shared chrome for every icon button in this component.
    $iconBtn = 'grid h-10 w-10 place-items-center rounded-xl border border-navy-100 bg-white text-slate-500 transition-all duration-200 ease-out-soft hover:-translate-y-0.5 hover:text-white active:translate-y-0';

    // Most-used networks in Indonesia first; each opens the network's own share dialog.
    $networks = [
        [
            'label' => 'WhatsApp',
            'href' => 'https://wa.me/?text='.$e($title.' — '.$shareUrl),
            'hover' => 'hover:border-[#25D366] hover:bg-[#25D366]',
            'path' => 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347M12.05 21.785h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0 0 20.465 3.488',
        ],
        [
            'label' => 'Facebook',
            'href' => 'https://www.facebook.com/sharer/sharer.php?u='.$e($shareUrl),
            'hover' => 'hover:border-[#1877F2] hover:bg-[#1877F2]',
            'path' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
        ],
        [
            'label' => 'X (Twitter)',
            'href' => 'https://twitter.com/intent/tweet?url='.$e($shareUrl).'&text='.$e($title),
            'hover' => 'hover:border-navy-900 hover:bg-navy-900',
            'path' => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z',
        ],
        [
            'label' => 'LinkedIn',
            'href' => 'https://www.linkedin.com/sharing/share-offsite/?url='.$e($shareUrl),
            'hover' => 'hover:border-[#0A66C2] hover:bg-[#0A66C2]',
            'path' => 'M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z',
        ],
        [
            'label' => 'Telegram',
            'href' => 'https://t.me/share/url?url='.$e($shareUrl).'&text='.$e($title),
            'hover' => 'hover:border-[#229ED9] hover:bg-[#229ED9]',
            'path' => 'M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z',
        ],
    ];
@endphp

<div x-data="shareArticle(@js($shareUrl), @js($title))" class="not-prose">
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($networks as $net)
            <a href="{{ $net['href'] }}" target="_blank" rel="noopener noreferrer"
               title="{{ $isId ? 'Bagikan ke' : 'Share on' }} {{ $net['label'] }}"
               aria-label="{{ $isId ? 'Bagikan ke' : 'Share on' }} {{ $net['label'] }}"
               class="{{ $iconBtn }} {{ $net['hover'] }}">
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="{{ $net['path'] }}"/>
                </svg>
            </a>
        @endforeach

        {{-- Email --}}
        <a href="mailto:?subject={{ rawurlencode($title) }}&body={{ rawurlencode($shareUrl) }}"
           title="{{ $isId ? 'Bagikan lewat Email' : 'Share via Email' }}"
           aria-label="{{ $isId ? 'Bagikan lewat Email' : 'Share via Email' }}"
           class="{{ $iconBtn }} hover:border-navy hover:bg-navy">
            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
            </svg>
        </a>

        {{-- Copy link --}}
        {{-- Icon-only below sm so every tile in the row stays a uniform 40px square
             and the row wraps evenly instead of leaving a ragged gap. --}}
        <button type="button" @click="copy()"
                :aria-label="copied ? @js($isId ? 'Tautan tersalin' : 'Link copied') : @js($isId ? 'Salin tautan' : 'Copy link')"
                class="group relative inline-flex h-10 w-10 items-center justify-center gap-2 rounded-xl border border-navy-100 bg-white text-sm font-medium text-slate-600 transition-all duration-200 ease-out-soft hover:-translate-y-0.5 hover:border-sky-500 hover:text-sky-700 active:translate-y-0 sm:w-auto sm:justify-start sm:px-3.5"
                :class="copied && 'border-sky-500 text-sky-700'">
            <svg x-show="!copied" class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
            <svg x-show="copied" x-cloak class="h-[17px] w-[17px] text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 6 9 17l-5-5"/>
            </svg>
            <span class="hidden sm:inline" x-text="copied ? @js($isId ? 'Tersalin' : 'Copied') : @js($isId ? 'Salin tautan' : 'Copy link')"></span>
        </button>

        {{-- Native share sheet — only where the browser supports it (mostly mobile) --}}
        <button type="button" x-show="canShareNatively" x-cloak @click="shareNatively()"
                aria-label="{{ $isId ? 'Bagikan lewat aplikasi lain' : 'Share via other apps' }}"
                class="{{ $iconBtn }} hover:border-navy hover:bg-navy sm:hidden">
            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                <path d="m8.59 13.51 6.83 3.98M15.41 6.51l-6.82 3.98"/>
            </svg>
        </button>
    </div>

    <p role="status" aria-live="polite" class="sr-only" x-text="copied ? @js($isId ? 'Tautan artikel tersalin' : 'Article link copied') : ''"></p>
</div>
