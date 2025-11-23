<x-layouts.dashboard>
    <div x-data="{ showModal: false }">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Location Management</h2>
                <p class="text-gray-500 text-sm mt-1">Manage and overview all registered attendance locations.</p>
            </div>
            <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Location
            </button>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location Name
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Latitude
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Longitude
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Radius (m)
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $locations = [
                            ['name' => 'Kampus E231', 'lat' => '34.0522', 'lon' => '-118.2437', 'rad' => 50],
                            ['name' => 'Kampus E344', 'lat' => '34.0530', 'lon' => '-118.2450', 'rad' => 30],
                            ['name' => 'Kampus G118', 'lat' => '34.0515', 'lon' => '-118.2420', 'rad' => 75],
                            ['name' => 'Kampus D642', 'lat' => '34.0545', 'lon' => '-118.2410', 'rad' => 100],
                            ['name' => 'Kampus F8531', 'lat' => '34.0505', 'lon' => '-118.2460', 'rad' => 40],
                        ];
                    @endphp

                    @foreach ($locations as $loc)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loc['name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loc['lat'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loc['lon'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loc['rad'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-2">
                            <button class="text-blue-600 hover:text-blue-900" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button class="text-red-600 hover:text-red-900" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div x-show="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
            <div @click.away="showModal = false" class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                
                <h3 class="text-lg font-bold text-gray-800 mb-4">Create New Location</h3>
                
                @include('admin.locations.create') 
            </div>
        </div>
    </div>
</x-layouts.dashboard>