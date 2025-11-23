<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white border-r border-gray-200 px-6 pb-4">
    <div class="flex h-16 shrink-0 items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-auto text-blue-600" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
        <span class="ml-3 text-xl font-bold text-gray-900">AbsenKu</span>
    </div>

    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    {{-- CONTOH MENU ITEM (Sesuaikan dengan Role nanti pakai @if) --}}

                    <li>
                        <a href="#" class="bg-blue-50 text-blue-600 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            <svg class="h-6 w-6 shrink-0 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="#" class="text-gray-700 hover:text-blue-600 hover:bg-blue-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition">
                            <svg class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Profil Saya
                        </a>
                    </li>

                    {{-- Tambahkan menu lain di sini (Jadwal, Laporan, dll) --}}

                </ul>
            </li>

            {{-- Profil User di Bawah Sidebar (Opsional, seperti di Visily) --}}
            <li class="mt-auto">
                 <a href="#" class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <img class="h-8 w-8 rounded-full bg-gray-50" src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=random" alt="">
                    <span class="sr-only">Your profile</span>
                    <span aria-hidden="true">{{ Auth::user()->name ?? 'Nama User' }}</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
