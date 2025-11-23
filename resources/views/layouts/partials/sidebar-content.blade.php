<!-- SIDEBAR MENU -->
<nav class="mt-6 flex-1">
    <ul class="-mx-2 space-y-1">
        <!-- Dashboard -->
        <li>
            <a href="{{ route('dashboard') }}"
                class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold
                leading-6 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                <i class="ph ph-gauge"></i>
                Dashboard
            </a>
        </li>

        <!-- Contoh Menu Tambahan -->
        <li>
            <a href="{{ route('absensi.index') }}"
                class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold
                leading-6 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                <i class="ph ph-check-square"></i>
                Absensi
            </a>
        </li>

        <!-- Tambah menu sesuai kebutuhan -->
    </ul>
</nav>

<!-- PROFILE (TIDAK MEMAKAI LINK LAGI) -->
<div class="mt-auto mb-3 px-2">
    <div class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold
        leading-6 text-gray-700 hover:bg-gray-100 transition cursor-default">
        <img class="h-8 w-8 rounded-full bg-gray-50"
            src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=random"
            alt="">
        <span>{{ Auth::user()->name ?? 'Nama User' }}</span>
    </div>
</div>

<!-- LOGOUT BUTTON (POST + STOP PROPAGATION) -->
<form method="POST" action="{{ route('logout') }}"
      onsubmit="event.stopPropagation();" class="px-2 pb-3">
    @csrf
    <button type="submit"
        onclick="event.stopPropagation();"
        class="w-full flex items-center gap-x-3 p-2 text-sm font-semibold leading-6
        text-red-600 hover:bg-red-50 rounded-md transition">
        <i class="ph ph-sign-out"></i>
        Keluar
    </button>
</form>
