@php $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.partnership')">
    <x-page-header
        :eyebrow="$id ? 'Kemitraan Corporate' : 'Corporate Partnership'"
        :title="$id ? 'Program Kemitraan Corporate Training' : 'Corporate Training Partnership Program'"
        :subtitle="$intro" />

    <section class="section">
        <div class="container">
            <p class="text-navy-500">{{ $id ? 'Halaman ini akan segera dilengkapi.' : 'This page will be completed soon.' }}</p>
        </div>
    </section>

    <x-cta-band />
</x-layout>
