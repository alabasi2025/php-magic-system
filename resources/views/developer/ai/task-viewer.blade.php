@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card bg-gradient-to-r from-blue-600 to-cyan-600 text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-tasks fa-3x"></i>
                        </div>
                        <div>
                            <h2 class="mb-1">📋 عارض سجلات المهام</h2>
                            <p class="mb-0">عرض تفاصيل المهام باستخدام Task ID</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task ID Input -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        البحث عن مهمة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input 
                            type="text" 
                            class="form-control" 
                            id="taskIdInput" 
                            placeholder="أدخل Task ID (مثال: JbGwE52i5MNMp6CTQSEZQr)"
                            value="JbGwE52i5MNMp6CTQSEZQr"
                        >
                        <button class="btn btn-primary" id="searchBtn">
                            <i class="fas fa-search me-1"></i>
                            بحث
                        </button>
                    </div>
                </div>
            </div>

            <!-- Task Details -->
            <div id="taskDetails" style="display: none;">
                <!-- Task Info Card -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            معلومات المهمة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Task ID:</strong> <code id="taskId"></code></p>
                                <p><strong>الحالة:</strong> <span id="taskStatus" class="badge"></span></p>
                                <p><strong>النموذج:</strong> <code id="taskModel"></code></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>تاريخ الإنشاء:</strong> <span id="taskCreatedAt"></span></p>
                                <p><strong>آخر تحديث:</strong> <span id="taskUpdatedAt"></span></p>
                                <p><strong>Credits المستخدمة:</strong> <span id="taskCredits" class="badge bg-warning"></span></p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <p><strong>رابط المهمة:</strong> <a id="taskUrl" href="#" target="_blank"></a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conversation History -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-comments me-2"></i>
                            سجل المحادثة
                        </h5>
                    </div>
                    <div class="card-body" id="conversationHistory">
                        <!-- Will be filled by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Loading -->
            <div id="loading" style="display: none;" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
                <p class="mt-3">جاري تحميل بيانات المهمة...</p>
            </div>

            <!-- Error -->
            <div id="error" style="display: none;" class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span id="errorMessage"></span>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchBtn').addEventListener('click', async function() {
    const taskId = document.getElementById('taskIdInput').value.trim();
    
    if (!taskId) {
        alert('الرجاء إدخال Task ID');
        return;
    }
    
    // Hide previous results
    document.getElementById('taskDetails').style.display = 'none';
    document.getElementById('error').style.display = 'none';
    document.getElementById('loading').style.display = 'block';
    
    try {
        const response = await fetch(`/developer/ai/task/${taskId}`);
        const data = await response.json();
        
        document.getElementById('loading').style.display = 'none';
        
        if (response.ok) {
            displayTaskDetails(data);
        } else {
            showError(data.error || 'فشل في تحميل بيانات المهمة');
        }
    } catch (error) {
        document.getElementById('loading').style.display = 'none';
        showError('خطأ في الاتصال: ' + error.message);
    }
});

function displayTaskDetails(task) {
    // Fill task info
    document.getElementById('taskId').textContent = task.id;
    document.getElementById('taskModel').textContent = task.model;
    document.getElementById('taskCredits').textContent = task.credit_usage || 0;
    
    // Status badge
    const statusBadge = document.getElementById('taskStatus');
    statusBadge.textContent = task.status;
    statusBadge.className = 'badge ' + getStatusClass(task.status);
    
    // Dates
    document.getElementById('taskCreatedAt').textContent = formatDate(task.created_at);
    document.getElementById('taskUpdatedAt').textContent = formatDate(task.updated_at);
    
    // Task URL
    if (task.metadata && task.metadata.task_url) {
        const taskUrl = document.getElementById('taskUrl');
        taskUrl.href = task.metadata.task_url;
        taskUrl.textContent = task.metadata.task_url;
    }
    
    // Conversation history
    const conversationDiv = document.getElementById('conversationHistory');
    conversationDiv.innerHTML = '';
    
    if (task.output && task.output.length > 0) {
        task.output.forEach(message => {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message mb-3 p-3 rounded ' + 
                (message.role === 'user' ? 'bg-light' : 'bg-info bg-opacity-10');
            
            const roleIcon = message.role === 'user' ? '👤' : '🤖';
            const roleName = message.role === 'user' ? 'المستخدم' : 'المساعد';
            
            messageDiv.innerHTML = `
                <div class="d-flex align-items-start">
                    <div class="me-3 fs-3">${roleIcon}</div>
                    <div class="flex-grow-1">
                        <strong>${roleName}</strong>
                        <p class="mb-0 mt-2">${escapeHtml(message.content[0]?.text || '')}</p>
                    </div>
                </div>
            `;
            
            conversationDiv.appendChild(messageDiv);
        });
    } else {
        conversationDiv.innerHTML = '<p class="text-muted">لا توجد رسائل</p>';
    }
    
    // Show task details
    document.getElementById('taskDetails').style.display = 'block';
}

function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('error').style.display = 'block';
}

function getStatusClass(status) {
    const classes = {
        'pending': 'bg-warning',
        'running': 'bg-info',
        'completed': 'bg-success',
        'failed': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

function formatDate(timestamp) {
    const date = new Date(parseInt(timestamp) * 1000);
    return date.toLocaleString('ar-SA', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/\n/g, '<br>');
}

// Auto-search on page load if Task ID is present
window.addEventListener('load', function() {
    const taskId = document.getElementById('taskIdInput').value;
    if (taskId) {
        document.getElementById('searchBtn').click();
    }
});
</script>

<style>
.bg-gradient-to-r {
    background: linear-gradient(to right, #2563eb, #0891b2);
}

.message {
    border-left: 4px solid #0891b2;
}
</style>
@endsection
