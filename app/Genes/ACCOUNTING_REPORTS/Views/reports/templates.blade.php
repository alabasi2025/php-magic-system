<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قوالب التقارير - نظام العباسي المحاسبي</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- العنوان -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">قوالب التقارير</h1>
            <p class="text-gray-600">إدارة وتخصيص قوالب التقارير المحاسبية</p>
        </div>

        <!-- زر إضافة قالب جديد -->
        <div class="mb-6">
            <button onclick="showCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                + إضافة قالب جديد
            </button>
        </div>

        <!-- جدول القوالب -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">اسم القالب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوصف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد التقارير</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($templates as $template)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $template->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ $template->template_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">{{ $template->description }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $template->generated_reports_count ?? 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($template->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">نشط</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">معطل</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button onclick="editTemplate({{ $template->id }})" class="text-blue-600 hover:text-blue-900 mr-3">تعديل</button>
                            <button onclick="toggleTemplate({{ $template->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                {{ $template->is_active ? 'تعطيل' : 'تفعيل' }}
                            </button>
                            <button onclick="deleteTemplate({{ $template->id }})" class="text-red-600 hover:text-red-900">حذف</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            لا توجد قوالب متاحة. قم بإنشاء قالب جديد للبدء.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($templates->hasPages())
        <div class="mt-6">
            {{ $templates->links() }}
        </div>
        @endif
    </div>

    <!-- Modal إنشاء قالب -->
    <div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">إنشاء قالب تقرير جديد</h3>
                <form action="{{ route('accounting-reports.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم القالب</label>
                            <input type="text" name="name" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نوع التقرير</label>
                            <select name="template_type" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">اختر النوع</option>
                                <option value="intermediate_accounts">الحسابات الوسيطة</option>
                                <option value="cash_boxes">الصناديق</option>
                                <option value="partners">الشراكات</option>
                                <option value="budgets">الميزانيات</option>
                                <option value="financial_summary">الملخص المالي</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                            <textarea name="description" rows="3" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الهيكل (JSON)</label>
                            <textarea name="structure" rows="5" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                                placeholder='{"columns": ["name", "amount"], "filters": []}'></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                        <button type="button" onclick="hideCreateModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                            إلغاء
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            حفظ القالب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function hideCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function editTemplate(id) {
            // Implement edit functionality
            alert('تعديل القالب #' + id);
        }

        function toggleTemplate(id) {
            if (confirm('هل أنت متأكد من تغيير حالة هذا القالب؟')) {
                // Implement toggle functionality
                window.location.href = '/accounting-reports/templates/' + id + '/toggle';
            }
        }

        function deleteTemplate(id) {
            if (confirm('هل أنت متأكد من حذف هذا القالب؟ لا يمكن التراجع عن هذا الإجراء.')) {
                // Implement delete functionality
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/accounting-reports/templates/' + id;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
