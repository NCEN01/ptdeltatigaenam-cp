@props(['expiresAt' => null])

@php
    use Illuminate\Support\Carbon;

    $isId = app()->getLocale() === 'id';

    /* Tiga keadaan, bukan sekadar tanggal mentah. Halaman ini dipakai untuk
       memeriksa keabsahan sertifikat, jadi pertanyaan pertama pembacanya adalah
       "masih berlaku atau tidak" — bukan "kapan tanggalnya".

       Warna hanya dipakai untuk dua keadaan yang perlu perhatian. Yang normal
       dibiarkan netral supaya daftar tidak menjadi ramai warna. */
    $today = Carbon::today();
    $expired = $expiresAt !== null && $expiresAt->lt($today);
    $soon = ! $expired && $expiresAt !== null && $expiresAt->lte($today->copy()->addDays(90));

    [$label, $tone, $dot] = match (true) {
        $expired => [$isId ? 'Kedaluwarsa' : 'Expired', 'border-rose-200 bg-rose-50 text-rose-700', 'bg-rose-500'],
        $soon => [$isId ? 'Segera berakhir' : 'Expiring soon', 'border-amber-200 bg-amber-50 text-amber-800', 'bg-amber-500'],
        default => [$isId ? 'Berlaku' : 'Valid', 'border-navy-100 bg-white text-navy', 'bg-sky-500'],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[11px] font-medium whitespace-nowrap {$tone}"]) }}>
    <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $dot }}" aria-hidden="true"></span>
    {{ $label }}
</span>
