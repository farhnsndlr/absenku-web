<x-layouts.dashboard>
    <div x-data="{ showModal: false }">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Session Management</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola semua jadwal sesi perkuliahan yang akan diadakan.</p>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-700">Daftar Sesi Aktif</h3>
            <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Sesi Baru
            </button>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Course / Code
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date / Time
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            SI201 - Software Engineering
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            25 Nov 2025 (10:00 - 11:30)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Kampus E231 (Radius 50m)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-2">
                            <button class="text-red-600 hover:text-red-900" title="Cancel Session">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div x-show="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
            <div @click.away="showModal = false" class="relative top-20 mx-auto p-5 border w-1/2 shadow-lg rounded-md bg-white">
                
                <h3 class="text-lg font-bold text-gray-800 mb-4">Buat Sesi Perkuliahan Baru</h3>
                
                @include('lecturer.sessions.create') 
            </div>
        </div>
    </div>
</x-layouts.dashboard>  