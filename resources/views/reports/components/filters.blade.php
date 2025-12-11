<div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 mb-6">
    <h3 class="text-lg font-bold text-white mb-4">🔍 الفلاتر</h3>
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-gray-300 text-sm mb-2">من تاريخ</label>
            <input type="date" name="start_date" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white">
        </div>
        <div>
            <label class="block text-gray-300 text-sm mb-2">إلى تاريخ</label>
            <input type="date" name="end_date" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white">
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                تطبيق
            </button>
        </div>
    </form>
</div>
