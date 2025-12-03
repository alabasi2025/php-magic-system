
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محلل الأداء</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
        .loader {
            border-top-color: #3498db;
            -webkit-animation: spin 1s linear infinite;
            animation: spin 1s linear infinite;
        }
        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-bold text-center mb-8">محلل أداء الكود</h1>

        
        <!-- قسم الإدخال والتحكم -->
        <div class="bg-white p-6 rounded-lg shadow-xl mb-8">
            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">1. إدخال الكود والتحكم</h2>

            <!-- اختيار نوع التحليل -->
            <div class="mb-4">
                <label for="analysis-type" class="block text-lg font-medium text-gray-700 mb-2">نوع التحليل المطلوب:</label>
                <select id="analysis-type" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    <option value="speed">تحليل السرعة والأداء العام</option>
                    <option value="bottlenecks">تحديد نقاط الاختناق (Bottlenecks)</option>
                    <option value="memory">استهلاك الذاكرة والموارد</option>
                    <option value="queries">تحليل استعلامات قواعد البيانات (Queries)</option>
                </select>
            </div>

            <!-- إدخال الكود -->
            <div class="mb-6">
                <label for="code-input" class="block text-lg font-medium text-gray-700 mb-2">الكود المراد تحليله (يفضل PHP):</label>
                <textarea id="code-input" rows="15" class="w-full p-4 border border-gray-300 rounded-lg font-mono text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="الصق كود PHP هنا..."></textarea>
            </div>

            <!-- زر التحليل -->
            <div class="flex justify-center">
                <button id="analyze-btn" class="flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10 transition duration-150 ease-in-out">
                    <i class="fas fa-cogs ml-2"></i>
                    <span>تحليل الكود</span>
                    <div id="loading-spinner" class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-6 w-6 ml-3 hidden"></div>
                </button>
            </div>
        </div>

        
        <!-- قسم عرض النتائج -->
        <div id="results-section" class="bg-white p-6 rounded-lg shadow-xl" style="display: none;">
            <h2 class="text-2xl font-semibold mb-6 border-b pb-2">2. نتائج التحليل</h2>

            <!-- Performance Score & Export Button -->
            <div class="flex justify-between items-center mb-6">
                <div class="text-center">
                    <p class="text-xl font-medium text-gray-600">نقاط الأداء</p>
                    <p id="performance-score" class="text-6xl font-bold text-green-600">--</p>
                </div>
                <button id="export-btn" class="flex items-center px-6 py-2 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition duration-150 ease-in-out">
                    <i class="fas fa-file-pdf ml-2"></i>
                    <span>تصدير التقرير (PDF)</span>
                </button>
            </div>

            <!-- Bottlenecks & Suggestions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="p-4 border border-red-300 rounded-lg bg-red-50">
                    <h3 class="text-xl font-semibold text-red-700 mb-3 flex items-center">
                        <i class="fas fa-exclamation-triangle ml-2"></i>
                        نقاط الاختناق المكتشفة (Bottlenecks)
                    </h3>
                    <ul id="bottlenecks-list" class="list-disc pr-5 text-red-600 space-y-2">
                        <li>لا توجد نقاط اختناق بعد.</li>
                    </ul>
                </div>
                <div class="p-4 border border-green-300 rounded-lg bg-green-50">
                    <h3 class="text-xl font-semibold text-green-700 mb-3 flex items-center">
                        <i class="fas fa-lightbulb ml-2"></i>
                        اقتراحات التحسين
                    </h3>
                    <ul id="suggestions-list" class="list-disc pr-5 text-green-600 space-y-2">
                        <li>ابدأ التحليل لعرض الاقتراحات.</li>
                    </ul>
                </div>
            </div>

            <!-- Performance Charts -->
            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-3 border-b pb-2">الرسوم البيانية للأداء</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg shadow">
                        <p class="text-center font-medium mb-2">توزيع وقت التنفيذ (مللي ثانية)</p>
                        <canvas id="timeChart"></canvas>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow">
                        <p class="text-center font-medium mb-2">استهلاك الذاكرة (ميغابايت)</p>
                        <canvas id="memoryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Detailed Report -->
            <div class="mb-4">
                <h3 class="text-xl font-semibold mb-3 border-b pb-2">التقرير المفصل</h3>
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <pre id="detailed-report" class="language-php text-sm whitespace-pre-wrap"><code>التقرير المفصل سيظهر هنا بعد التحليل.</code></pre>
                </div>
            </div>
        </div>

        
        <!-- شيفرة JavaScript -->
        <script>
            // محاكاة بيانات النتائج
            const mockResults = {
                score: 85,
                bottlenecks: [
                    "استعلام قاعدة بيانات بطيء في السطر 45.",
                    "حلقة تكرارية غير فعالة في الدالة 'processData'."
                ],
                suggestions: [
                    "استخدم التخزين المؤقت (Caching) لنتائج الاستعلامات المتكررة.",
                    "استبدل الحلقة التكرارية بـ array_map أو array_filter لتحسين الأداء.",
                    "تأكد من استخدام الفهارس (Indexes) المناسبة في قاعدة البيانات."
                ],
                detailedReport: "تقرير مفصل:\n- وقت التنفيذ: 1500 مللي ثانية\n- استهلاك الذاكرة الأقصى: 48 ميغابايت\n- عدد استعلامات قاعدة البيانات: 12\n- تفاصيل إضافية عن كل خطوة...",
                timeData: [300, 500, 150, 700, 250],
                memoryData: [12, 18, 10, 25, 15]
            };

            const analyzeBtn = document.getElementById('analyze-btn');
            const loadingSpinner = document.getElementById('loading-spinner');
            const resultsSection = document.getElementById('results-section');
            const codeInput = document.getElementById('code-input');
            const analysisType = document.getElementById('analysis-type');
            const performanceScore = document.getElementById('performance-score');
            const bottlenecksList = document.getElementById('bottlenecks-list');
            const suggestionsList = document.getElementById('suggestions-list');
            const detailedReport = document.getElementById('detailed-report');
            const exportBtn = document.getElementById('export-btn');

            let timeChart, memoryChart;

            // تهيئة الرسوم البيانية
            function initCharts() {
                const timeCtx = document.getElementById('timeChart').getContext('2d');
                timeChart = new Chart(timeCtx, {
                    type: 'bar',
                    data: {
                        labels: ['الخطوة 1', 'الخطوة 2', 'الخطوة 3', 'الخطوة 4', 'الخطوة 5'],
                        datasets: [{
                            label: 'وقت التنفيذ (مللي ثانية)',
                            data: mockResults.timeData,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                const memoryCtx = document.getElementById('memoryChart').getContext('2d');
                memoryChart = new Chart(memoryCtx, {
                    type: 'line',
                    data: {
                        labels: ['الخطوة 1', 'الخطوة 2', 'الخطوة 3', 'الخطوة 4', 'الخطوة 5'],
                        datasets: [{
                            label: 'استهلاك الذاكرة (ميغابايت)',
                            data: mockResults.memoryData,
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // تحديث الرسوم البيانية
            function updateCharts(timeData, memoryData) {
                timeChart.data.datasets[0].data = timeData;
                memoryChart.data.datasets[0].data = memoryData;
                timeChart.update();
                memoryChart.update();
            }

            // تحديث واجهة المستخدم بالنتائج
            function updateUI(results) {
                // تحديث نقاط الأداء
                performanceScore.textContent = results.score;
                performanceScore.className = performanceScore.className.replace(/text-(red|yellow|green)-\d{3}/, '');
                if (results.score >= 80) {
                    performanceScore.classList.add('text-green-600');
                } else if (results.score >= 50) {
                    performanceScore.classList.add('text-yellow-600');
                } else {
                    performanceScore.classList.add('text-red-600');
                }

                // تحديث نقاط الاختناق
                bottlenecksList.innerHTML = '';
                results.bottlenecks.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    bottlenecksList.appendChild(li);
                });

                // تحديث الاقتراحات
                suggestionsList.innerHTML = '';
                results.suggestions.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    suggestionsList.appendChild(li);
                });

                // تحديث التقرير المفصل وتلوين الأكواد
                detailedReport.innerHTML = `<code class="language-php">${results.detailedReport}</code>`;
                Prism.highlightElement(detailedReport.querySelector('code'));

                // تحديث الرسوم البيانية
                updateCharts(results.timeData, results.memoryData);

                // إظهار قسم النتائج
                resultsSection.style.display = 'block';
            }

            // محاكاة عملية التحليل (AJAX)
            analyzeBtn.addEventListener('click', () => {
                const code = codeInput.value;
                const type = analysisType.value;

                if (!code.trim()) {
                    alert('الرجاء إدخال الكود المراد تحليله.');
                    return;
                }

                // تفعيل حالة التحميل
                analyzeBtn.disabled = true;
                loadingSpinner.classList.remove('hidden');
                analyzeBtn.querySelector('span').textContent = 'جاري التحليل...';

                // محاكاة طلب AJAX
                setTimeout(() => {
                    // هنا يتم إرسال طلب AJAX إلى الـ backend
                    // fetch('/api/analyze', { method: 'POST', body: JSON.stringify({ code, type }) })
                    // .then(response => response.json())
                    // .then(data => {
                    //     updateUI(data);
                    // })
                    // .catch(error => {
                    //     alert('حدث خطأ أثناء التحليل: ' + error);
                    // })
                    // .finally(() => {
                    //     // تعطيل حالة التحميل
                    //     analyzeBtn.disabled = false;
                    //     loadingSpinner.classList.add('hidden');
                    //     analyzeBtn.querySelector('span').textContent = 'تحليل الكود';
                    // });

                    // استخدام البيانات الوهمية للمحاكاة
                    const simulatedResults = {
                        score: Math.floor(Math.random() * 100),
                        bottlenecks: mockResults.bottlenecks.slice(0, Math.floor(Math.random() * 3) + 1),
                        suggestions: mockResults.suggestions.slice(0, Math.floor(Math.random() * 3) + 1),
                        detailedReport: mockResults.detailedReport.replace('1500', Math.floor(Math.random() * 2000) + 500).replace('48', Math.floor(Math.random() * 100) + 20),
                        timeData: mockResults.timeData.map(d => d + Math.floor(Math.random() * 200) - 100),
                        memoryData: mockResults.memoryData.map(d => d + Math.floor(Math.random() * 10) - 5)
                    };
                    updateUI(simulatedResults);

                    // تعطيل حالة التحميل
                    analyzeBtn.disabled = false;
                    loadingSpinner.classList.add('hidden');
                    analyzeBtn.querySelector('span').textContent = 'تحليل الكود';

                }, 2000); // محاكاة وقت التحليل
            });

            // محاكاة زر التصدير
            exportBtn.addEventListener('click', () => {
                alert('تم محاكاة تصدير التقرير إلى PDF.');
                // هنا يمكن إضافة منطق لتصدير محتوى الصفحة أو طلب تقرير PDF من الـ backend
            });

            // تهيئة محرر الكود (محاكاة تلوين الأكواد عند التحميل الأولي)
            codeInput.value = `<?php

// مثال على كود PHP غير فعال
function processData($data) {
    $result = [];
    // حلقة تكرارية غير فعالة
    for ($i = 0; $i < count($data); $i++) {
        // استعلام قاعدة بيانات بطيء (محاكاة)
        $dbResult = DB::query("SELECT * FROM items WHERE id = " . $data[$i]); // السطر 45
        if ($dbResult) {
            $result[] = $dbResult;
        }
    }
    return $result;
}

$largeArray = range(1, 1000);
processData($largeArray);

?>`;
            
            // تهيئة الرسوم البيانية عند تحميل الصفحة
            window.onload = initCharts;

        </script>




    </div>

</body>
</html>
</html>
