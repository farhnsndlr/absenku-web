<div class="space-y-1">
    <a href="{{ route('student.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all
       {{ request()->routeIs('student.dashboard') ? 'text-blue-700 bg-blue-50 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        <svg class="w-[20px] h-[20px] {{ request()->routeIs('student.dashboard') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span>Beranda</span>
    </a>
    <a href="{{ route('student.attendance.index') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all
       {{ request()->routeIs('student.attendance.*') ? 'text-blue-700 bg-blue-50 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        <svg class="w-[20px] h-[20px] {{ request()->routeIs('student.attendance.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
        <span>Absensi (Check-In)</span>
    </a>
</div>
