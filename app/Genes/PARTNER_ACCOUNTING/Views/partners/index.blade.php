
<div x-data="{}" class="bg-gray-100 p-8 font-sans">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Total Partners</h3>
            <p class="text-3xl font-bold text-gray-900">1,250</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Total Participation</h3>
            <p class="text-3xl font-bold text-gray-900">85%</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Total Balance</h3>
            <p class="text-3xl font-bold text-green-600">$5,400,000</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-700 mb-2">New Partners (Last 30 Days)</h3>
            <p class="text-3xl font-bold text-blue-600">42</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 flex justify-between items-center border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Partners List</h2>
            <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Partner
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partner Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participation (%)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Sample Row 1 -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="https://i.pravatar.cc/150?img=1" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">John Doe</div>
                                    <div class="text-sm text-gray-500">john.doe@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">15%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">$1,200,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900 mr-3">Delete</button>
                            <button class="text-green-600 hover:text-green-900">View Account</button>
                        </td>
                    </tr>

                    <!-- Sample Row 2 -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="https://i.pravatar.cc/150?img=2" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Jane Smith</div>
                                    <div class="text-sm text-gray-500">jane.smith@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">10%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">$800,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900 mr-3">Delete</button>
                            <button class="text-green-600 hover:text-green-900">View Account</button>
                        </td>
                    </tr>

                    <!-- Add more partner rows as needed -->

                </tbody>
            </table>
        </div>
    </div>
</div>
