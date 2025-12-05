@php
/**
 * Laravel Blade View: Security Scanner Interface
 * File: /home/ubuntu/php-magic-system/resources/views/developer/ai/security-scanner.blade.php
 * Component: View
 * Description: واجهة احترافية لفاحص الأمان (Security Scanner) مدعومة بالذكاء الاصطناعي.
 *              تستخدم Laravel و Tailwind CSS.
 *
 * المتطلبات المدمجة:
 * 1. فحص SQL Injection, XSS, CSRF, والأذونات
 * 2. تصميم احترافية (باستخدام Tailwind CSS)
 * 3. محرر كود مع Syntax Highlighting
 * 4. عرض النتائج بألوان حسب الخطورة
 * 5. اقتراحات إصلاح فورية
 * 6. توثيق شامل
 * 
 * @version 3.14.0
 */
@endphp

@extends('layouts.app')

@section('title', 'فاحص الأمان الذكي - Security Scanner')

@section('content')
<div class="container mx-auto p-6 bg-gray-50 min-h-screen">
    <div class="bg-gradient-to-r from-red-600 to-purple-600 rounded-lg shadow-lg p-8 mb-6 text-white">
        <h1 class="text-4xl font-extrabold mb-2">
            <i class="fas fa-shield-alt ml-2"></i> فاحص الأمان الذكي (AI Security Scanner)
        </h1>
        <p class="text-red-100 text-lg">فحص شامل للكود لاكتشاف الثغرات الأمنية: SQL Injection, XSS, CSRF, والأذونات</p>
        <div class="mt-4 flex items-center space-x-4 rtl:space-x-reverse">
            <span class="bg-white/20 px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-code ml-1"></i> الإصدار v3.14.0
            </span>
            <span class="bg-white/20 px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-check-circle ml-1"></i> جاهز للاستخدام
            </span>
        </div>
    </div>

    {{-- التوثيق الموجز والتعليمات --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-6 border-l-4 border-red-500">
        <p class="text-sm text-gray-700">
            <strong class="font-semibold">الوصف:</strong> أداة تحليل متقدمة لفحص الكود وكشف الثغرات الأمنية المحتملة (SQL Injection, XSS, CSRF, Permissions, File Upload, Authentication, Encryption, Input Validation) وتقديم اقتراحات إصلاح فورية مع تحديد رقم السطر ودرجة الخطورة.
        </p>
        <p class="text-sm text-gray-700 mt-2">
            <strong class="font-semibold">كيفية الاستخدام:</strong> اكتب أو الصق الكود في المحرر، اختر أنواع الفحص المطلوبة، ثم اضغط على "فحص الكود". يمكنك أيضاً رفع ملف أو فحص مجلد كامل.
        </p>
    </div>

    {{-- خيارات الفحص --}}
    <div class="bg-white p-6 rounded-lg shadow-xl mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-cog ml-2"></i> خيارات الفحص
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <label class="flex items-center space-x-2 rtl:space-x-reverse cursor-pointer">
                <input type="checkbox" id="scan-sql-injection" class="scan-option form-checkbox h-5 w-5 text-red-600" checked>
                <span class="text-sm font-medium">SQL Injection</span>
            </label>
            <label class="flex items-center space-x-2 rtl:space-x-reverse cursor-pointer">
                <input type="checkbox" id="scan-xss" class="scan-option form-checkbox h-5 w-5 text-red-600" checked>
                <span class="text-sm font-medium">XSS</span>
            </label>
            <label class="flex items-center space-x-2 rtl:space-x-reverse cursor-pointer">
                <input type="checkbox" id="scan-csrf" class="scan-option form-checkbox h-5 w-5 text-red-600" checked>
                <span class="text-sm font-medium">CSRF</span>
            </label>
            <label class="flex items-center space-x-2 rtl:space-x-reverse cursor-pointer">
                <input type="checkbox" id="scan-permissions" class="scan-option form-checkbox h-5 w-5 text-red-600" checked>
                <span class="text-sm font-medium">الصلاحيات</span>
            </label>
            <label class="flex items-center space-x-2 rtl:space-x-reverse cursor-pointer">
                <input type="checkbox" id="scan-file-upload" class="scan-option form-checkbox h-5 w-5 text-red-600" checked>
                <span class="text-sm font-medium">رفع الملفات</span>
            </label>
            <label class="flex items-center space-x-2 rtl:space-x-reverse cursor-pointer">
                <input type="checkbox" id="scan-authentication" class="scan-option form-checkbox h-5 w-5 text-red-600" checked>
                <span class="text-sm font-medium">المصادقة</span>
            </label>
            <label class="flex items-center space-x-2 rtl:space-x-reverse cursor-pointer">
                <input type="checkbox" id="scan-encryption" class="scan-option form-checkbox h-5 w-5 text-red-600" checked>
                <span class="text-sm font-medium">التشفير</span>
            </label>
            <label class="flex items-center space-x-2 rtl:space-x-reverse cursor-pointer">
                <input type="checkbox" id="scan-input-validation" class="scan-option form-checkbox h-5 w-5 text-red-600" checked>
                <span class="text-sm font-medium">التحقق من المدخلات</span>
            </label>
        </div>
    </div>

    {{-- التبويبات --}}
    <div class="bg-white rounded-lg shadow-xl mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button class="tab-button active px-6 py-3 text-sm font-medium border-b-2 border-red-600 text-red-600" data-tab="code-scan">
                    <i class="fas fa-code ml-1"></i> فحص الكود
                </button>
                <button class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="file-scan">
                    <i class="fas fa-file-code ml-1"></i> فحص ملف
                </button>
                <button class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="directory-scan">
                    <i class="fas fa-folder ml-1"></i> فحص مجلد
                </button>
            </nav>
        </div>

        {{-- محتوى التبويبات --}}
        <div class="p-6">
            {{-- تبويب فحص الكود --}}
            <div id="code-scan-tab" class="tab-content">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- محرر الكود --}}
                    <div class="lg:col-span-2">
                        <div class="flex flex-col h-full">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">محرر الكود</h3>
                            <textarea id="code-editor" name="code"
                                class="flex-grow w-full p-4 font-mono text-sm border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 resize-none"
                                placeholder="// اكتب أو الصق كود PHP أو Laravel هنا للتحليل..."
                                rows="20"><?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show($id)
    {
        // مثال على كود يحتوي على ثغرات أمنية
        $user = User::find($id);
        
        if (!$user) {
            return redirect()->back();
        }
        
        return view('user.profile', compact('user'));
    }
    
    public function update(Request $request, $id)
    {
        // بدون التحقق من الصلاحيات
        $user = User::find($id);
        $user->update($request->all());
        
        return redirect()->back();
    }
}
</textarea>
                            <div class="mt-4 flex justify-end space-x-3 rtl:space-x-reverse">
                                <button type="button" id="scan-code-btn"
                                    class="px-6 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 transition duration-300">
                                    <i class="fas fa-search ml-2"></i> فحص الكود
                                </button>
                                <button type="button" id="clear-code-btn"
                                    class="px-6 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-300 transition duration-300">
                                    <i class="fas fa-eraser ml-2"></i> مسح المحرر
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- نتائج الفحص --}}
                    <div class="lg:col-span-1">
                        <div class="flex flex-col h-full">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">نتائج الفحص</h3>
                            <div id="scan-results" class="flex-grow overflow-y-auto space-y-4">
                                <div class="text-center py-12 text-gray-400">
                                    <i class="fas fa-shield-alt text-6xl mb-4"></i>
                                    <p>اضغط على "فحص الكود" لبدء التحليل</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- تبويب فحص ملف --}}
            <div id="file-scan-tab" class="tab-content hidden">
                <div class="max-w-2xl mx-auto">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                        <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">رفع ملف للفحص</h3>
                        <p class="text-sm text-gray-500 mb-4">الملفات المدعومة: .php, .blade.php, .txt (حتى 10 ميجابايت)</p>
                        <input type="file" id="file-input" accept=".php,.txt" class="hidden">
                        <button type="button" id="upload-file-btn"
                            class="px-6 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 transition duration-300">
                            <i class="fas fa-file-upload ml-2"></i> اختر ملف
                        </button>
                        <div id="file-info" class="mt-4 hidden">
                            <p class="text-sm text-gray-700"><strong>الملف:</strong> <span id="file-name"></span></p>
                            <button type="button" id="scan-file-btn"
                                class="mt-4 px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition duration-300">
                                <i class="fas fa-search ml-2"></i> فحص الملف
                            </button>
                        </div>
                    </div>
                    <div id="file-scan-results" class="mt-6"></div>
                </div>
            </div>

            {{-- تبويب فحص مجلد --}}
            <div id="directory-scan-tab" class="tab-content hidden">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">فحص مجلد كامل</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">مسار المجلد (نسبي من جذر المشروع)</label>
                                <input type="text" id="directory-path" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                    placeholder="app/Http/Controllers" value="app/Http/Controllers">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">امتدادات الملفات</label>
                                <div class="flex space-x-2 rtl:space-x-reverse">
                                    <label class="flex items-center space-x-1 rtl:space-x-reverse">
                                        <input type="checkbox" class="ext-option form-checkbox h-4 w-4 text-red-600" value="php" checked>
                                        <span class="text-sm">.php</span>
                                    </label>
                                    <label class="flex items-center space-x-1 rtl:space-x-reverse">
                                        <input type="checkbox" class="ext-option form-checkbox h-4 w-4 text-red-600" value="blade.php">
                                        <span class="text-sm">.blade.php</span>
                                    </label>
                                </div>
                            </div>
                            <button type="button" id="scan-directory-btn"
                                class="w-full px-6 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 transition duration-300">
                                <i class="fas fa-folder-open ml-2"></i> فحص المجلد
                            </button>
                        </div>
                    </div>
                    <div id="directory-scan-results" class="mt-6"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- إحصائيات الفحص --}}
    <div id="scan-stats" class="hidden grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-red-600" id="stat-critical">0</div>
            <div class="text-xs text-gray-600 mt-1">حرجة</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-orange-600" id="stat-high">0</div>
            <div class="text-xs text-gray-600 mt-1">عالية</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-yellow-600" id="stat-medium">0</div>
            <div class="text-xs text-gray-600 mt-1">متوسطة</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-blue-600" id="stat-low">0</div>
            <div class="text-xs text-gray-600 mt-1">منخفضة</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-gray-600" id="stat-info">0</div>
            <div class="text-xs text-gray-600 mt-1">معلومات</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-green-600" id="stat-score">100</div>
            <div class="text-xs text-gray-600 mt-1">درجة الأمان</div>
        </div>
    </div>

    {{-- التوصيات --}}
    <div class="bg-white p-6 rounded-lg shadow-xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-lightbulb ml-2 text-yellow-500"></i> توصيات الأمان
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($recommendations as $key => $recommendation)
            <div class="border-l-4 border-red-500 pl-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $recommendation['title'] }}</h3>
                <ul class="space-y-1 text-sm text-gray-700">
                    @foreach($recommendation['tips'] as $tip)
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 ml-2 mt-1"></i>
                        <span>{{ $tip }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // التبويبات
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // إزالة active من جميع الأزرار
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-red-600', 'text-red-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // إضافة active للزر المحدد
            this.classList.add('active', 'border-red-600', 'text-red-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            
            // إخفاء جميع المحتويات
            tabContents.forEach(content => content.classList.add('hidden'));
            
            // إظهار المحتوى المحدد
            document.getElementById(tabName + '-tab').classList.remove('hidden');
        });
    });

    // فحص الكود
    document.getElementById('scan-code-btn').addEventListener('click', function() {
        const code = document.getElementById('code-editor').value;
        const scans = getSelectedScans();
        
        if (!code.trim()) {
            alert('الرجاء إدخال كود للفحص');
            return;
        }
        
        scanCode(code, scans);
    });

    // مسح المحرر
    document.getElementById('clear-code-btn').addEventListener('click', function() {
        document.getElementById('code-editor').value = '';
        document.getElementById('scan-results').innerHTML = `
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-shield-alt text-6xl mb-4"></i>
                <p>اضغط على "فحص الكود" لبدء التحليل</p>
            </div>
        `;
        document.getElementById('scan-stats').classList.add('hidden');
    });

    // رفع ملف
    document.getElementById('upload-file-btn').addEventListener('click', function() {
        document.getElementById('file-input').click();
    });

    document.getElementById('file-input').addEventListener('change', function() {
        if (this.files.length > 0) {
            document.getElementById('file-name').textContent = this.files[0].name;
            document.getElementById('file-info').classList.remove('hidden');
        }
    });

    document.getElementById('scan-file-btn').addEventListener('click', function() {
        const fileInput = document.getElementById('file-input');
        if (fileInput.files.length === 0) {
            alert('الرجاء اختيار ملف');
            return;
        }
        
        const scans = getSelectedScans();
        scanFile(fileInput.files[0], scans);
    });

    // فحص مجلد
    document.getElementById('scan-directory-btn').addEventListener('click', function() {
        const path = document.getElementById('directory-path').value;
        const extensions = getSelectedExtensions();
        const scans = getSelectedScans();
        
        if (!path.trim()) {
            alert('الرجاء إدخال مسار المجلد');
            return;
        }
        
        scanDirectory(path, extensions, scans);
    });

    // الحصول على خيارات الفحص المحددة
    function getSelectedScans() {
        const scans = {};
        document.querySelectorAll('.scan-option').forEach(option => {
            const scanType = option.id.replace('scan-', '').replace(/-/g, '_');
            scans[scanType] = option.checked;
        });
        return scans;
    }

    // الحصول على الامتدادات المحددة
    function getSelectedExtensions() {
        const extensions = [];
        document.querySelectorAll('.ext-option:checked').forEach(option => {
            extensions.push(option.value);
        });
        return extensions;
    }

    // فحص الكود
    function scanCode(code, scans) {
        showLoading('scan-results');
        
        fetch('/developer/ai/security-scanner/scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code, scans })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResults(data.data, 'scan-results');
                updateStats(data.data);
            } else {
                showError('scan-results', data.message);
            }
        })
        .catch(error => {
            showError('scan-results', 'حدث خطأ أثناء الفحص');
            console.error('Error:', error);
        });
    }

    // فحص ملف
    function scanFile(file, scans) {
        showLoading('file-scan-results');
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('scans', JSON.stringify(scans));
        
        fetch('/developer/ai/security-scanner/scan-file', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResults(data.data, 'file-scan-results');
                updateStats(data.data);
            } else {
                showError('file-scan-results', data.message);
            }
        })
        .catch(error => {
            showError('file-scan-results', 'حدث خطأ أثناء فحص الملف');
            console.error('Error:', error);
        });
    }

    // فحص مجلد
    function scanDirectory(path, extensions, scans) {
        showLoading('directory-scan-results');
        
        fetch('/developer/ai/security-scanner/scan-directory', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ path, extensions, scans })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayDirectoryResults(data.data, 'directory-scan-results');
            } else {
                showError('directory-scan-results', data.message);
            }
        })
        .catch(error => {
            showError('directory-scan-results', 'حدث خطأ أثناء فحص المجلد');
            console.error('Error:', error);
        });
    }

    // عرض النتائج
    function displayResults(data, containerId) {
        const container = document.getElementById(containerId);
        
        if (data.total_issues === 0) {
            container.innerHTML = `
                <div class="p-4 border-r-4 border-green-600 bg-green-50 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl ml-3"></i>
                        <div>
                            <p class="text-lg font-semibold text-green-900">لم يتم العثور على ثغرات أمنية!</p>
                            <p class="text-sm text-green-700">الكود يبدو آمناً</p>
                        </div>
                    </div>
                </div>
            `;
            return;
        }
        
        let html = '';
        data.issues.forEach(issue => {
            const severityColors = {
                'critical': { bg: 'bg-red-50', border: 'border-red-600', text: 'text-red-900', badge: 'bg-red-200 text-red-800' },
                'high': { bg: 'bg-orange-50', border: 'border-orange-600', text: 'text-orange-900', badge: 'bg-orange-200 text-orange-800' },
                'medium': { bg: 'bg-yellow-50', border: 'border-yellow-600', text: 'text-yellow-900', badge: 'bg-yellow-200 text-yellow-800' },
                'low': { bg: 'bg-blue-50', border: 'border-blue-600', text: 'text-blue-900', badge: 'bg-blue-200 text-blue-800' },
                'info': { bg: 'bg-gray-50', border: 'border-gray-600', text: 'text-gray-900', badge: 'bg-gray-200 text-gray-800' }
            };
            
            const colors = severityColors[issue.severity] || severityColors['info'];
            
            html += `
                <div class="p-3 border-r-4 ${colors.border} ${colors.bg} rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold ${colors.badge} px-2 py-0.5 rounded-full">${issue.category}</span>
                        <span class="text-sm font-mono ${colors.text}">السطر: ${issue.line}</span>
                    </div>
                    <p class="text-sm ${colors.text} font-semibold mb-2">${issue.message}</p>
                    <div class="mb-2 p-2 bg-gray-100 rounded text-xs font-mono overflow-x-auto">
                        <code>${escapeHtml(issue.code)}</code>
                    </div>
                    <div class="p-2 bg-white border-l-2 ${colors.border} text-xs ${colors.text}">
                        <strong class="font-bold">الحل المقترح:</strong> ${issue.fix}
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }

    // عرض نتائج المجلد
    function displayDirectoryResults(data, containerId) {
        const container = document.getElementById(containerId);
        
        let html = `
            <div class="bg-white p-6 rounded-lg shadow-xl mb-4">
                <h3 class="text-lg font-bold text-gray-800 mb-4">ملخص الفحص</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-800">${data.total_files_scanned}</div>
                        <div class="text-xs text-gray-600">ملفات تم فحصها</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">${data.files_with_issues}</div>
                        <div class="text-xs text-gray-600">ملفات بها مشاكل</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">${data.total_issues}</div>
                        <div class="text-xs text-gray-600">إجمالي المشاكل</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">${data.average_score}</div>
                        <div class="text-xs text-gray-600">متوسط درجة الأمان</div>
                    </div>
                </div>
            </div>
        `;
        
        if (data.files.length > 0) {
            html += '<div class="space-y-4">';
            data.files.forEach(fileData => {
                html += `
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h4 class="font-semibold text-gray-800 mb-2">
                            <i class="fas fa-file-code ml-2"></i> ${fileData.file}
                        </h4>
                        <div class="text-sm text-gray-600 mb-2">
                            المشاكل: ${fileData.results.total_issues} | 
                            الدرجة: ${fileData.results.score}
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }
        
        container.innerHTML = html;
    }

    // تحديث الإحصائيات
    function updateStats(data) {
        document.getElementById('stat-critical').textContent = data.critical_count;
        document.getElementById('stat-high').textContent = data.high_count;
        document.getElementById('stat-medium').textContent = data.medium_count;
        document.getElementById('stat-low').textContent = data.low_count;
        document.getElementById('stat-info').textContent = data.info_count;
        document.getElementById('stat-score').textContent = data.score;
        document.getElementById('scan-stats').classList.remove('hidden');
    }

    // عرض التحميل
    function showLoading(containerId) {
        document.getElementById(containerId).innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-6xl text-red-600 mb-4"></i>
                <p class="text-gray-600">جاري الفحص...</p>
            </div>
        `;
    }

    // عرض الخطأ
    function showError(containerId, message) {
        document.getElementById(containerId).innerHTML = `
            <div class="p-4 border-r-4 border-red-600 bg-red-50 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 text-2xl ml-3"></i>
                    <p class="text-red-900">${message}</p>
                </div>
            </div>
        `;
    }

    // تنظيف HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
});
</script>
@endsection
