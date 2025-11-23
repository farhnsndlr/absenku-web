<form class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1" for="campus_name">Campus Name</label>
        <input id="campus_name" type="text" value="Kampus G Gunadarma" class="w-full border-gray-300 rounded-md shadow-sm" />
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1" for="latitude">Latitude</label>
        <input id="latitude" type="text" value="-39274.24" class="w-full border-gray-300 rounded-md shadow-sm" />
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1" for="longitude">Longitude</label>
        <input id="longitude" type="text" value="-923842934" class="w-full border-gray-300 rounded-md shadow-sm" />
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1" for="radius">Tolerance Radius</label>
        <input id="radius" type="number" value="100" class="w-full border-gray-300 rounded-md shadow-sm" />
    </div>

    <div class="h-32 bg-gray-200 flex items-center justify-center rounded-lg text-gray-500 border border-dashed border-gray-400">
        <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        Map View Placeholder
    </div>

    <div class="pt-4 flex justify-end space-x-3 border-t">
        <button type="button" @click="showModal = false" class="text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg font-medium transition">
            Cancel
        </button>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
            Save Changes
        </button>
    </div>
</form>