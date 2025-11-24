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
