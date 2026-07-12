@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'value' => null,
    'placeholder' => null,
    'autocomplete' => null,
])

@php $val = old($name, $value); $invalid = $errors->has($name); @endphp

<div class="{{ $type === 'textarea' ? '' : '' }}">
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-navy">
        {{ $label }}@if ($required)<span class="text-gold" aria-hidden="true"> *</span>@endif
    </label>

    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="5"
            @if ($required) required aria-required="true" @endif
            @if ($invalid) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            placeholder="{{ $placeholder }}"
            {{ $attributes->class([
                'w-full rounded-2xl border bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-0',
                'border-rose-300' => $invalid,
                'border-navy-200 focus:border-navy' => ! $invalid,
            ]) }}>{{ $val }}</textarea>
    @elseif ($type === 'password')
        <div class="relative" x-data="{ show: false }">
            <input id="{{ $name }}" name="{{ $name }}" x-bind:type="show ? 'text' : 'password'" value="{{ $val }}"
                @if ($required) required aria-required="true" @endif
                @if ($invalid) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
                autocomplete="{{ $autocomplete ?? match($name) {
                    'password' => 'current-password',
                    'password_confirmation' => 'new-password',
                    default => 'off',
                } }}"
                placeholder="{{ $placeholder }}"
                {{ $attributes->class([
                    'w-full rounded-2xl border bg-white px-4 py-3 pr-12 text-navy shadow-sm transition-colors placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-gold',
                    'border-rose-300' => $invalid,
                    'border-navy-200 focus:border-navy' => ! $invalid,
                ]) }}>
            <button type="button"
                x-on:click="show = !show"
                class="absolute right-3 top-1/2 grid h-7 w-7 -translate-y-1/2 place-items-center rounded-lg text-slate-500 transition-colors hover:text-navy"
                aria-label="{{ app()->getLocale() === 'id' ? 'Lihat/sembunyikan sandi' : 'Show/hide password' }}"
                tabindex="-1">
                {{-- Eye icon — closed --}}
                <svg x-show="!show" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3.2"/>
                    <line x1="4" x2="20" y1="4" y2="20"/>
                </svg>
                {{-- Eye icon — open --}}
                <svg x-show="show" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3.2"/>
                </svg>
            </button>
        </div>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $val }}"
            @if ($required) required aria-required="true" @endif
            @if ($invalid) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            autocomplete="{{ $autocomplete ?? match($name) {
                'email' => 'email',
                'phone' => 'tel',
                default => 'off',
            } }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->class([
                'w-full rounded-2xl border bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-gold',
                'border-rose-300' => $invalid,
                'border-navy-200 focus:border-navy' => ! $invalid,
            ]) }}>
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 text-sm text-rose-600" role="alert">{{ $message }}</p>
    @enderror
</div>