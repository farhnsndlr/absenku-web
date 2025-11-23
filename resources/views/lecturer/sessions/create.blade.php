<form class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1" for="course_id">Course Name:</label>
        <select id="course_id" class="w-full border-gray-300 rounded-md shadow-sm">
            <option>Pilih Mata Kuliah...</option>
            <option>Pemrograman Berbasis Web</option>
            <option>Struktur Data</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1" for="location_id">Lokasi Absensi</label>
        <select id="location_id" class="w-full border-gray-300 rounded-md shadow-sm">
            <option>Pilih Lokasi...</option>
            <option>Kampus E231 (Radius 50m)</option>
            <option>Kampus D642 (Radius 100m)</option>
        </select>
    </div>
    
    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="session_date">Tanggal</label>
            <input id="session_date" type="date" class="w-full border-gray-300 rounded-md shadow-sm" />
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="start_time">Mulai</label>
            <input id="start_time" type="time" value="10:00" class="w-full border-gray-300 rounded-md shadow-sm" />
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="end_time">Selesai</label>
            <input id="end_time" type="time" value="11:30" class="w-full border-gray-300 rounded-md shadow-sm" />
        </div>
    </div>

    <div class="pt-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">Session Type</label>
        <div class="flex items-center space-x-6">
            <div class="flex items-center">
                <input id="online_type" name="session_type" type="radio" checked class="h-4 w-4 text-blue-600 border-gray-300" />
                <label for="online_type" class="ml-2 block text-sm text-gray-700">Online</label>
            </div>
            <div class="flex items-center">
                <input id="onsite_type" name="session_type" type="radio" class="h-4 w-4 text-blue-600 border-gray-300" />
                <label for="onsite_type" class="ml-2 block text-sm text-gray-700">Onsite</label>
            </div>
        </div>
    </div>
    
    <div class="pt-4 flex justify-end space-x-3 border-t mt-6">
        <button type="button" @click="$parent.showModal = false" class="text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg font-medium transition">
            Cancel
        </button>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
            Create Session
        </button>
    </div>
</form>