# 🔌 دليل تكامل API - عرض سجلات المهام

## 📋 نظرة عامة

هذا الدليل يشرح كيفية دمج **API Endpoint** لعرض سجلات المهام في نظامك الخاص باستخدام لغات برمجة مختلفة.

---

## 🔗 معلومات API

### Endpoint
```
GET https://php-magic-system-main-4kqldr.laravel.cloud/developer/ai/task/{taskId}
```

### Parameters
| المعامل | النوع | الوصف | مثال |
|---------|------|-------|------|
| `taskId` | string | معرّف المهمة | `JbGwE52i5MNMp6CTQSEZQr` |

### Response Format
```json
{
  "id": "string",
  "object": "task",
  "created_at": "timestamp",
  "updated_at": "timestamp",
  "status": "completed|pending|running|failed",
  "model": "string",
  "metadata": {},
  "output": [],
  "credit_usage": 0
}
```

---

## 💻 أمثلة التكامل

### 1. JavaScript (Fetch API)

```javascript
/**
 * الحصول على تفاصيل مهمة
 * @param {string} taskId - معرّف المهمة
 * @returns {Promise<Object>} - بيانات المهمة
 */
async function getTaskDetails(taskId) {
    const baseUrl = 'https://php-magic-system-main-4kqldr.laravel.cloud';
    const url = `${baseUrl}/developer/ai/task/${taskId}`;
    
    try {
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        return data;
        
    } catch (error) {
        console.error('Error fetching task:', error);
        throw error;
    }
}

// الاستخدام
getTaskDetails('JbGwE52i5MNMp6CTQSEZQr')
    .then(task => {
        console.log('Task ID:', task.id);
        console.log('Status:', task.status);
        console.log('Model:', task.model);
        console.log('Credits:', task.credit_usage);
        console.log('Messages:', task.output.length);
    })
    .catch(error => {
        console.error('Failed to get task:', error);
    });
```

### 2. JavaScript (Axios)

```javascript
const axios = require('axios');

/**
 * الحصول على تفاصيل مهمة باستخدام Axios
 */
async function getTaskDetails(taskId) {
    const baseUrl = 'https://php-magic-system-main-4kqldr.laravel.cloud';
    
    try {
        const response = await axios.get(`${baseUrl}/developer/ai/task/${taskId}`);
        return response.data;
        
    } catch (error) {
        if (error.response) {
            // الخادم رد بخطأ
            console.error('Error response:', error.response.data);
            console.error('Status code:', error.response.status);
        } else if (error.request) {
            // الطلب أُرسل لكن لم يُستقبل رد
            console.error('No response received:', error.request);
        } else {
            // خطأ في إعداد الطلب
            console.error('Error:', error.message);
        }
        throw error;
    }
}

// الاستخدام
getTaskDetails('JbGwE52i5MNMp6CTQSEZQr')
    .then(task => {
        console.log('Task:', task);
    });
```

### 3. PHP (cURL)

```php
<?php

/**
 * الحصول على تفاصيل مهمة
 * 
 * @param string $taskId معرّف المهمة
 * @return array|null بيانات المهمة أو null عند الفشل
 */
function getTaskDetails($taskId) {
    $baseUrl = 'https://php-magic-system-main-4kqldr.laravel.cloud';
    $url = "{$baseUrl}/developer/ai/task/{$taskId}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        error_log("cURL Error: {$error}");
        return null;
    }
    
    curl_close($ch);
    
    if ($httpCode !== 200) {
        error_log("HTTP Error: {$httpCode}");
        return null;
    }
    
    return json_decode($response, true);
}

// الاستخدام
$taskId = 'JbGwE52i5MNMp6CTQSEZQr';
$task = getTaskDetails($taskId);

if ($task) {
    echo "Task ID: " . $task['id'] . "\n";
    echo "Status: " . $task['status'] . "\n";
    echo "Model: " . $task['model'] . "\n";
    echo "Credits: " . $task['credit_usage'] . "\n";
    echo "Messages: " . count($task['output']) . "\n";
} else {
    echo "Failed to get task details\n";
}
```

### 4. PHP (Guzzle HTTP)

```php
<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * الحصول على تفاصيل مهمة باستخدام Guzzle
 */
function getTaskDetails($taskId) {
    $baseUrl = 'https://php-magic-system-main-4kqldr.laravel.cloud';
    $client = new Client([
        'base_uri' => $baseUrl,
        'timeout' => 30,
    ]);
    
    try {
        $response = $client->get("/developer/ai/task/{$taskId}");
        return json_decode($response->getBody(), true);
        
    } catch (GuzzleException $e) {
        error_log("Guzzle Error: " . $e->getMessage());
        return null;
    }
}

// الاستخدام
$task = getTaskDetails('JbGwE52i5MNMp6CTQSEZQr');

if ($task) {
    print_r($task);
}
```

### 5. Python (requests)

```python
import requests
from typing import Optional, Dict

def get_task_details(task_id: str) -> Optional[Dict]:
    """
    الحصول على تفاصيل مهمة
    
    Args:
        task_id: معرّف المهمة
        
    Returns:
        بيانات المهمة أو None عند الفشل
    """
    base_url = 'https://php-magic-system-main-4kqldr.laravel.cloud'
    url = f'{base_url}/developer/ai/task/{task_id}'
    
    try:
        response = requests.get(url, timeout=30)
        response.raise_for_status()
        return response.json()
        
    except requests.exceptions.RequestException as e:
        print(f'Error: {e}')
        return None

# الاستخدام
task_id = 'JbGwE52i5MNMp6CTQSEZQr'
task = get_task_details(task_id)

if task:
    print(f"Task ID: {task['id']}")
    print(f"Status: {task['status']}")
    print(f"Model: {task['model']}")
    print(f"Credits: {task['credit_usage']}")
    print(f"Messages: {len(task['output'])}")
```

### 6. cURL (Command Line)

```bash
#!/bin/bash

# الحصول على تفاصيل مهمة
TASK_ID="JbGwE52i5MNMp6CTQSEZQr"
BASE_URL="https://php-magic-system-main-4kqldr.laravel.cloud"

curl -X GET \
  "${BASE_URL}/developer/ai/task/${TASK_ID}" \
  -H "Accept: application/json" \
  -w "\nHTTP Status: %{http_code}\n" \
  | jq '.'
```

### 7. jQuery (AJAX)

```javascript
/**
 * الحصول على تفاصيل مهمة باستخدام jQuery
 */
function getTaskDetails(taskId) {
    const baseUrl = 'https://php-magic-system-main-4kqldr.laravel.cloud';
    
    return $.ajax({
        url: `${baseUrl}/developer/ai/task/${taskId}`,
        method: 'GET',
        dataType: 'json',
        timeout: 30000,
        success: function(data) {
            console.log('Task loaded successfully:', data);
        },
        error: function(xhr, status, error) {
            console.error('Error loading task:', error);
        }
    });
}

// الاستخدام
getTaskDetails('JbGwE52i5MNMp6CTQSEZQr')
    .done(function(task) {
        $('#taskId').text(task.id);
        $('#taskStatus').text(task.status);
        $('#taskModel').text(task.model);
        $('#taskCredits').text(task.credit_usage);
    })
    .fail(function() {
        alert('فشل تحميل بيانات المهمة');
    });
```

---

## 🎯 حالات استخدام متقدمة

### 1. عرض سجل المحادثة

```javascript
async function displayConversation(taskId) {
    const task = await getTaskDetails(taskId);
    
    if (!task || !task.output) {
        console.error('No conversation found');
        return;
    }
    
    const conversationDiv = document.getElementById('conversation');
    conversationDiv.innerHTML = '';
    
    task.output.forEach(message => {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${message.role}`;
        
        const roleIcon = message.role === 'user' ? '👤' : '🤖';
        const text = message.content[0]?.text || '';
        
        messageDiv.innerHTML = `
            <div class="message-header">
                <span class="icon">${roleIcon}</span>
                <span class="role">${message.role}</span>
            </div>
            <div class="message-text">${text}</div>
        `;
        
        conversationDiv.appendChild(messageDiv);
    });
}
```

### 2. مراقبة حالة المهمة (Polling)

```javascript
/**
 * مراقبة حالة المهمة حتى تكتمل
 */
async function waitForTaskCompletion(taskId, maxAttempts = 60, interval = 2000) {
    for (let i = 0; i < maxAttempts; i++) {
        const task = await getTaskDetails(taskId);
        
        if (!task) {
            throw new Error('Failed to get task details');
        }
        
        console.log(`Attempt ${i + 1}: Status = ${task.status}`);
        
        if (task.status === 'completed') {
            console.log('Task completed!');
            return task;
        }
        
        if (task.status === 'failed') {
            throw new Error(`Task failed: ${task.error}`);
        }
        
        // انتظر قبل المحاولة التالية
        await new Promise(resolve => setTimeout(resolve, interval));
    }
    
    throw new Error('Task did not complete in time');
}

// الاستخدام
waitForTaskCompletion('JbGwE52i5MNMp6CTQSEZQr')
    .then(task => {
        console.log('Final task:', task);
    })
    .catch(error => {
        console.error('Error:', error);
    });
```

### 3. حساب إحصائيات المهام

```php
<?php

/**
 * حساب إحصائيات مجموعة من المهام
 */
function calculateTaskStats(array $taskIds) {
    $stats = [
        'total' => count($taskIds),
        'completed' => 0,
        'failed' => 0,
        'pending' => 0,
        'running' => 0,
        'total_credits' => 0,
        'total_messages' => 0,
    ];
    
    foreach ($taskIds as $taskId) {
        $task = getTaskDetails($taskId);
        
        if (!$task) {
            continue;
        }
        
        // عدّ الحالات
        $stats[$task['status']]++;
        
        // جمع Credits
        $stats['total_credits'] += $task['credit_usage'] ?? 0;
        
        // عدّ الرسائل
        $stats['total_messages'] += count($task['output'] ?? []);
    }
    
    return $stats;
}

// الاستخدام
$taskIds = [
    'JbGwE52i5MNMp6CTQSEZQr',
    'another-task-id-here',
];

$stats = calculateTaskStats($taskIds);
print_r($stats);
```

### 4. تصدير سجل المحادثة إلى Markdown

```javascript
/**
 * تصدير سجل المحادثة إلى Markdown
 */
async function exportConversationToMarkdown(taskId) {
    const task = await getTaskDetails(taskId);
    
    if (!task || !task.output) {
        return null;
    }
    
    let markdown = `# سجل المحادثة - ${task.id}\n\n`;
    markdown += `**التاريخ:** ${new Date(task.created_at * 1000).toLocaleString('ar-SA')}\n`;
    markdown += `**النموذج:** ${task.model}\n`;
    markdown += `**الحالة:** ${task.status}\n\n`;
    markdown += `---\n\n`;
    
    task.output.forEach((message, index) => {
        const role = message.role === 'user' ? '👤 المستخدم' : '🤖 المساعد';
        const text = message.content[0]?.text || '';
        
        markdown += `## ${role}\n\n`;
        markdown += `${text}\n\n`;
    });
    
    markdown += `---\n\n`;
    markdown += `**Credits المستخدمة:** ${task.credit_usage}\n`;
    
    return markdown;
}

// الاستخدام
exportConversationToMarkdown('JbGwE52i5MNMp6CTQSEZQr')
    .then(markdown => {
        // تنزيل الملف
        const blob = new Blob([markdown], { type: 'text/markdown' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'conversation.md';
        a.click();
    });
```

---

## 🔒 معالجة الأخطاء

### مثال شامل لمعالجة الأخطاء

```javascript
async function getTaskDetailsWithErrorHandling(taskId) {
    const baseUrl = 'https://php-magic-system-main-4kqldr.laravel.cloud';
    const url = `${baseUrl}/developer/ai/task/${taskId}`;
    
    try {
        const response = await fetch(url);
        
        // معالجة أخطاء HTTP
        if (!response.ok) {
            switch (response.status) {
                case 404:
                    throw new Error('المهمة غير موجودة');
                case 400:
                    throw new Error('Task ID غير صحيح');
                case 500:
                    throw new Error('خطأ في الخادم');
                default:
                    throw new Error(`خطأ HTTP: ${response.status}`);
            }
        }
        
        const data = await response.json();
        
        // التحقق من صحة البيانات
        if (!data.id || !data.status) {
            throw new Error('بيانات غير صحيحة');
        }
        
        return data;
        
    } catch (error) {
        if (error instanceof TypeError) {
            // خطأ في الشبكة
            console.error('خطأ في الاتصال بالإنترنت');
        } else {
            console.error('خطأ:', error.message);
        }
        
        // إعادة رمي الخطأ للمعالجة الخارجية
        throw error;
    }
}
```

---

## 📊 أمثلة واجهات المستخدم

### 1. عرض بطاقة المهمة (HTML + CSS)

```html
<div class="task-card" id="taskCard">
    <div class="task-header">
        <h3>Task ID: <span id="taskId"></span></h3>
        <span class="status-badge" id="statusBadge"></span>
    </div>
    
    <div class="task-info">
        <p><strong>النموذج:</strong> <span id="taskModel"></span></p>
        <p><strong>التاريخ:</strong> <span id="taskDate"></span></p>
        <p><strong>Credits:</strong> <span id="taskCredits"></span></p>
    </div>
    
    <div class="task-conversation" id="conversation">
        <!-- سيتم ملؤها بـ JavaScript -->
    </div>
</div>

<style>
.task-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eee;
}

.status-badge {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.status-badge.completed {
    background-color: #28a745;
    color: white;
}

.status-badge.pending {
    background-color: #ffc107;
    color: black;
}

.status-badge.failed {
    background-color: #dc3545;
    color: white;
}

.message {
    margin: 15px 0;
    padding: 15px;
    border-radius: 8px;
}

.message.user {
    background-color: #f0f0f0;
    margin-left: 20px;
}

.message.assistant {
    background-color: #e3f2fd;
    margin-right: 20px;
}
</style>

<script>
async function loadAndDisplayTask(taskId) {
    const task = await getTaskDetails(taskId);
    
    // ملء البيانات
    document.getElementById('taskId').textContent = task.id;
    document.getElementById('taskModel').textContent = task.model;
    document.getElementById('taskCredits').textContent = task.credit_usage;
    document.getElementById('taskDate').textContent = 
        new Date(task.created_at * 1000).toLocaleString('ar-SA');
    
    // Status badge
    const statusBadge = document.getElementById('statusBadge');
    statusBadge.textContent = task.status;
    statusBadge.className = `status-badge ${task.status}`;
    
    // عرض المحادثة
    displayConversation(taskId);
}
</script>
```

---

## 🚀 نصائح الأداء

### 1. التخزين المؤقت (Caching)

```javascript
// تخزين مؤقت بسيط
const taskCache = new Map();

async function getTaskDetailsWithCache(taskId, ttl = 60000) {
    // التحقق من الـ cache
    if (taskCache.has(taskId)) {
        const cached = taskCache.get(taskId);
        if (Date.now() - cached.timestamp < ttl) {
            return cached.data;
        }
    }
    
    // جلب البيانات
    const data = await getTaskDetails(taskId);
    
    // حفظ في الـ cache
    taskCache.set(taskId, {
        data: data,
        timestamp: Date.now()
    });
    
    return data;
}
```

### 2. Batch Requests

```javascript
/**
 * جلب عدة مهام دفعة واحدة
 */
async function getMultipleTasks(taskIds) {
    const promises = taskIds.map(id => getTaskDetails(id));
    return Promise.allSettled(promises);
}

// الاستخدام
const taskIds = ['task1', 'task2', 'task3'];
const results = await getMultipleTasks(taskIds);

results.forEach((result, index) => {
    if (result.status === 'fulfilled') {
        console.log(`Task ${taskIds[index]}:`, result.value);
    } else {
        console.error(`Failed to load task ${taskIds[index]}:`, result.reason);
    }
});
```

---

## ✅ الخلاصة

**API Endpoint جاهز للاستخدام في أي نظام!**

- 🔗 Endpoint: `GET /developer/ai/task/{taskId}`
- ✅ يدعم جميع لغات البرمجة
- ✅ سهل التكامل
- ✅ معالجة أخطاء احترافية
- ✅ أمثلة جاهزة للاستخدام

**ابدأ الآن:**
```
https://php-magic-system-main-4kqldr.laravel.cloud/developer/ai/task/JbGwE52i5MNMp6CTQSEZQr
```
