# 📚 أمثلة استخدام مكتبات Task API Client

## 🎯 نظرة عامة

تم إنشاء مكتبتين للتكامل السهل مع Task API:
1. **JavaScript** - `task-api-client.js`
2. **PHP** - `TaskAPIClient.php`

---

## 🔷 JavaScript Library

### التضمين في صفحة HTML

```html
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Task Viewer</title>
</head>
<body>
    <!-- تضمين المكتبة -->
    <script src="/js/task-api-client.js"></script>
    
    <script>
        // استخدام المكتبة
        const client = new TaskAPIClient();
        
        client.getTask('JbGwE52i5MNMp6CTQSEZQr')
            .then(task => {
                console.log('Task:', task);
            });
    </script>
</body>
</html>
```

### مثال 1: عرض تفاصيل مهمة

```javascript
// إنشاء client
const client = new TaskAPIClient({
    baseUrl: 'https://php-magic-system-main-4kqldr.laravel.cloud',
    cache: true,
    cacheTTL: 60000 // دقيقة واحدة
});

// الحصول على المهمة
async function displayTask(taskId) {
    try {
        const task = await client.getTask(taskId);
        
        document.getElementById('taskId').textContent = task.id;
        document.getElementById('status').textContent = task.status;
        document.getElementById('model').textContent = task.model;
        document.getElementById('credits').textContent = task.credit_usage;
        
        console.log('Task loaded successfully');
        
    } catch (error) {
        console.error('Error:', error.message);
        alert('فشل تحميل المهمة');
    }
}

displayTask('JbGwE52i5MNMp6CTQSEZQr');
```

### مثال 2: عرض المحادثة

```javascript
async function displayConversation(taskId) {
    const client = new TaskAPIClient();
    const messages = await client.getConversation(taskId);
    
    const container = document.getElementById('conversation');
    container.innerHTML = '';
    
    messages.forEach(message => {
        const div = document.createElement('div');
        div.className = `message ${message.role}`;
        
        const icon = message.role === 'user' ? '👤' : '🤖';
        const text = message.content[0]?.text || '';
        
        div.innerHTML = `
            <div class="message-header">
                <span>${icon} ${message.role}</span>
            </div>
            <div class="message-text">${text}</div>
        `;
        
        container.appendChild(div);
    });
}
```

### مثال 3: مراقبة المهمة

```javascript
async function monitorTask(taskId) {
    const client = new TaskAPIClient();
    
    try {
        const task = await client.waitForCompletion(taskId, {
            maxAttempts: 30,
            interval: 3000, // 3 ثواني
            onProgress: (progress) => {
                console.log(`محاولة ${progress.attempt}/${progress.maxAttempts}`);
                console.log(`الحالة: ${progress.status}`);
                
                // تحديث الواجهة
                document.getElementById('status').textContent = progress.status;
                document.getElementById('attempt').textContent = 
                    `${progress.attempt}/${progress.maxAttempts}`;
            }
        });
        
        console.log('اكتملت المهمة!', task);
        alert('اكتملت المهمة بنجاح');
        
    } catch (error) {
        console.error('خطأ:', error.message);
        alert('فشلت المهمة أو انتهت المهلة');
    }
}
```

### مثال 4: الحصول على إحصائيات

```javascript
async function showTaskStats(taskId) {
    const client = new TaskAPIClient();
    const stats = await client.getTaskStats(taskId);
    
    console.log('إحصائيات المهمة:');
    console.log('- Task ID:', stats.id);
    console.log('- الحالة:', stats.status);
    console.log('- النموذج:', stats.model);
    console.log('- Credits:', stats.credits);
    console.log('- عدد الرسائل:', stats.messageCount);
    console.log('- رسائل المستخدم:', stats.userMessages);
    console.log('- رسائل المساعد:', stats.assistantMessages);
    console.log('- المدة:', stats.duration, 'ثانية');
    console.log('- رابط المهمة:', stats.taskUrl);
    
    return stats;
}
```

### مثال 5: تصدير المحادثة

```javascript
async function exportToMarkdown(taskId) {
    const client = new TaskAPIClient();
    const markdown = await client.exportConversation(taskId, 'markdown');
    
    // تنزيل الملف
    const blob = new Blob([markdown], { type: 'text/markdown;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `conversation-${taskId}.md`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
```

### مثال 6: عدة مهام

```javascript
async function loadMultipleTasks() {
    const client = new TaskAPIClient();
    const taskIds = ['task1', 'task2', 'task3'];
    
    const tasks = await client.getMultipleTasks(taskIds);
    
    tasks.forEach((task, index) => {
        if (task.error) {
            console.error(`خطأ في المهمة ${taskIds[index]}:`, task.message);
        } else {
            console.log(`المهمة ${index + 1}:`, task.status);
        }
    });
}
```

---

## 🔶 PHP Library

### الاستخدام في Laravel Controller

```php
<?php

namespace App\Http\Controllers;

use App\Services\TaskAPIClient;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected TaskAPIClient $taskClient;
    
    public function __construct()
    {
        $this->taskClient = new TaskAPIClient([
            'base_url' => 'https://php-magic-system-main-4kqldr.laravel.cloud',
            'cache' => true,
            'cache_ttl' => 60
        ]);
    }
    
    public function show($taskId)
    {
        try {
            $task = $this->taskClient->getTask($taskId);
            return view('tasks.show', compact('task'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'فشل تحميل المهمة: ' . $e->getMessage());
        }
    }
}
```

### مثال 1: عرض تفاصيل مهمة

```php
<?php

use App\Services\TaskAPIClient;

$client = new TaskAPIClient();

try {
    $task = $client->getTask('JbGwE52i5MNMp6CTQSEZQr');
    
    echo "Task ID: {$task['id']}\n";
    echo "Status: {$task['status']}\n";
    echo "Model: {$task['model']}\n";
    echo "Credits: {$task['credit_usage']}\n";
    
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
```

### مثال 2: الحصول على المحادثة

```php
<?php

$client = new TaskAPIClient();
$messages = $client->getConversation('JbGwE52i5MNMp6CTQSEZQr');

foreach ($messages as $message) {
    $role = $message['role'] === 'user' ? 'المستخدم' : 'المساعد';
    $text = $message['content'][0]['text'] ?? '';
    
    echo "[{$role}]\n";
    echo "{$text}\n\n";
}
```

### مثال 3: مراقبة المهمة

```php
<?php

$client = new TaskAPIClient();

try {
    $task = $client->waitForCompletion('JbGwE52i5MNMp6CTQSEZQr', [
        'max_attempts' => 30,
        'interval' => 3,
        'on_progress' => function($progress) {
            echo "محاولة {$progress['attempt']}/{$progress['max_attempts']}\n";
            echo "الحالة: {$progress['status']}\n";
        }
    ]);
    
    echo "اكتملت المهمة!\n";
    print_r($task);
    
} catch (\Exception $e) {
    echo "خطأ: {$e->getMessage()}\n";
}
```

### مثال 4: إحصائيات المهمة

```php
<?php

$client = new TaskAPIClient();
$stats = $client->getTaskStats('JbGwE52i5MNMp6CTQSEZQr');

echo "إحصائيات المهمة:\n";
echo "- Task ID: {$stats['id']}\n";
echo "- الحالة: {$stats['status']}\n";
echo "- النموذج: {$stats['model']}\n";
echo "- Credits: {$stats['credits']}\n";
echo "- عدد الرسائل: {$stats['message_count']}\n";
echo "- رسائل المستخدم: {$stats['user_messages']}\n";
echo "- رسائل المساعد: {$stats['assistant_messages']}\n";
echo "- المدة: {$stats['duration']} ثانية\n";
echo "- التاريخ: {$stats['created_at']->format('Y-m-d H:i:s')}\n";
```

### مثال 5: تصدير المحادثة

```php
<?php

$client = new TaskAPIClient();

// تصدير إلى Markdown
$markdown = $client->exportConversation('JbGwE52i5MNMp6CTQSEZQr', 'markdown');
file_put_contents('conversation.md', $markdown);

// تصدير إلى JSON
$json = $client->exportConversation('JbGwE52i5MNMp6CTQSEZQr', 'json');
file_put_contents('conversation.json', $json);

// تصدير إلى نص عادي
$text = $client->exportConversation('JbGwE52i5MNMp6CTQSEZQr', 'text');
file_put_contents('conversation.txt', $text);
```

### مثال 6: إحصائيات مجمّعة

```php
<?php

$client = new TaskAPIClient();
$taskIds = ['task1', 'task2', 'task3'];

$stats = $client->calculateBatchStats($taskIds);

echo "إحصائيات مجمّعة:\n";
echo "- إجمالي المهام: {$stats['total']}\n";
echo "- مكتملة: {$stats['completed']}\n";
echo "- فاشلة: {$stats['failed']}\n";
echo "- قيد الانتظار: {$stats['pending']}\n";
echo "- قيد التشغيل: {$stats['running']}\n";
echo "- إجمالي Credits: {$stats['total_credits']}\n";
echo "- إجمالي الرسائل: {$stats['total_messages']}\n";
```

### مثال 7: استخدام في Blade Template

```blade
@php
    $client = new \App\Services\TaskAPIClient();
    $task = $client->getTask($taskId);
    $stats = $client->getTaskStats($taskId);
@endphp

<div class="task-card">
    <h3>{{ $task['id'] }}</h3>
    
    <div class="task-info">
        <p><strong>الحالة:</strong> {{ $task['status'] }}</p>
        <p><strong>النموذج:</strong> {{ $task['model'] }}</p>
        <p><strong>Credits:</strong> {{ $stats['credits'] }}</p>
        <p><strong>الرسائل:</strong> {{ $stats['message_count'] }}</p>
    </div>
    
    <div class="conversation">
        @foreach($client->getConversation($taskId) as $message)
            <div class="message {{ $message['role'] }}">
                <strong>{{ $message['role'] === 'user' ? 'المستخدم' : 'المساعد' }}</strong>
                <p>{{ $message['content'][0]['text'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
</div>
```

---

## 🎨 أمثلة واجهات كاملة

### مثال: صفحة عرض المهمة (HTML + JavaScript)

```html
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عارض المهام</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .task-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-completed { background: #28a745; color: white; }
        .status-pending { background: #ffc107; color: black; }
        .status-failed { background: #dc3545; color: white; }
        
        .message {
            margin: 15px 0;
            padding: 15px;
            border-radius: 8px;
        }
        
        .message.user {
            background: #f0f0f0;
            margin-left: 40px;
        }
        
        .message.assistant {
            background: #e3f2fd;
            margin-right: 40px;
        }
        
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1>عارض المهام</h1>
    
    <div class="task-card">
        <input type="text" id="taskIdInput" placeholder="أدخل Task ID" 
               value="JbGwE52i5MNMp6CTQSEZQr" style="width: 100%; padding: 10px;">
        <button onclick="loadTask()">تحميل المهمة</button>
        <button onclick="exportMarkdown()">تصدير Markdown</button>
    </div>
    
    <div id="taskDetails" style="display: none;">
        <div class="task-card">
            <h2>معلومات المهمة</h2>
            <p><strong>Task ID:</strong> <span id="taskId"></span></p>
            <p><strong>الحالة:</strong> <span id="status" class="status-badge"></span></p>
            <p><strong>النموذج:</strong> <span id="model"></span></p>
            <p><strong>Credits:</strong> <span id="credits"></span></p>
        </div>
        
        <div class="task-card">
            <h2>سجل المحادثة</h2>
            <div id="conversation"></div>
        </div>
    </div>
    
    <script src="/js/task-api-client.js"></script>
    <script>
        const client = new TaskAPIClient();
        let currentTask = null;
        
        async function loadTask() {
            const taskId = document.getElementById('taskIdInput').value;
            
            try {
                currentTask = await client.getTask(taskId);
                displayTask(currentTask);
            } catch (error) {
                alert('خطأ: ' + error.message);
            }
        }
        
        function displayTask(task) {
            document.getElementById('taskId').textContent = task.id;
            document.getElementById('model').textContent = task.model;
            document.getElementById('credits').textContent = task.credit_usage;
            
            const statusBadge = document.getElementById('status');
            statusBadge.textContent = task.status;
            statusBadge.className = `status-badge status-${task.status}`;
            
            const conversation = document.getElementById('conversation');
            conversation.innerHTML = '';
            
            (task.output || []).forEach(message => {
                const div = document.createElement('div');
                div.className = `message ${message.role}`;
                
                const icon = message.role === 'user' ? '👤' : '🤖';
                const text = message.content[0]?.text || '';
                
                div.innerHTML = `
                    <strong>${icon} ${message.role}</strong>
                    <p>${text}</p>
                `;
                
                conversation.appendChild(div);
            });
            
            document.getElementById('taskDetails').style.display = 'block';
        }
        
        async function exportMarkdown() {
            if (!currentTask) {
                alert('الرجاء تحميل مهمة أولاً');
                return;
            }
            
            const markdown = await client.exportConversation(currentTask.id, 'markdown');
            
            const blob = new Blob([markdown], { type: 'text/markdown' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `conversation-${currentTask.id}.md`;
            a.click();
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
```

---

## ✅ الخلاصة

**المكتبات جاهزة للاستخدام!**

### JavaScript
- 📁 الملف: `/public/js/task-api-client.js`
- ✅ Class-based API
- ✅ Promise-based
- ✅ Built-in caching
- ✅ Error handling

### PHP
- 📁 الملف: `/app/Services/TaskAPIClient.php`
- ✅ Laravel integration
- ✅ Cache support
- ✅ Type hints
- ✅ Exception handling

**ابدأ الاستخدام الآن:**
```javascript
const client = new TaskAPIClient();
const task = await client.getTask('JbGwE52i5MNMp6CTQSEZQr');
```

```php
$client = new TaskAPIClient();
$task = $client->getTask('JbGwE52i5MNMp6CTQSEZQr');
```
