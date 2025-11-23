<x-layouts.guest>
    
    <form>
        <div class="mb-4">
            <label class="block font-medium text-sm text-gray-700" for="email">Email</label>
            <input class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" id="email" type="email" name="email" required autofocus />
        </div>

        <div class="mt-4">
            <label class="block font-medium text-sm text-gray-700" for="password">Password</label>
            <input class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" id="password" type="password" name="password" required />
        </div>

        <div class="flex items-center justify-end mt-6">
            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                Masuk
            </button>
        </div>
    </form>

</x-layouts.guest>