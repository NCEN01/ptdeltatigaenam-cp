@php $id = app()->getLocale() === 'id'; @endphp
<nav class="flex flex-col gap-1" aria-label="Account">
    <a href="{{ route('account.profile') }}"
       class="rounded-2xl px-4 py-3 text-sm font-medium transition-colors {{ request()->routeIs('account.profile') ? 'bg-navy text-white' : 'text-navy-600 hover:bg-mist' }}">
        {{ $id ? 'Profil' : 'Profile' }}
    </a>
    <a href="{{ route('account.orders') }}"
       class="rounded-2xl px-4 py-3 text-sm font-medium transition-colors {{ request()->routeIs('account.orders') ? 'bg-navy text-white' : 'text-navy-600 hover:bg-mist' }}">
        {{ $id ? 'Riwayat Pesanan' : 'Order History' }}
    </a>
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="w-full rounded-2xl px-4 py-3 text-left text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50">
            {{ $id ? 'Keluar' : 'Sign out' }}
        </button>
    </form>
</nav>
