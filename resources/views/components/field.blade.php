@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'value' => null,
    'placeholder' => null,
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
                'w-full rounded-2xl border bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-navy-300 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-0',
                'border-rose-300' => $invalid,
                'border-navy-200 focus:border-navy' => ! $invalid,
            ]) }}>{{ $val }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $val }}"
            @if ($required) required aria-required="true" @endif
            @if ($invalid) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            autocomplete="{{ $name === 'email' ? 'email' : ($name === 'phone' ? 'tel' : 'on') }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->class([
                'w-full rounded-2xl border bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-navy-300 focus:outline-none focus:ring-2 focus:ring-gold',
                'border-rose-300' => $invalid,
                'border-navy-200 focus:border-navy' => ! $invalid,
            ]) }}>
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 text-sm text-rose-600" role="alert">{{ $message }}</p>
    @enderror
</div>
