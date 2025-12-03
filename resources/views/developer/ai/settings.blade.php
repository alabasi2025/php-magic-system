@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card bg-gradient-to-r from-purple-600 to-pink-600 text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-cog fa-3x"></i>
                        </div>
                        <div>
                            <h2 class="mb-1">⚙️ إعدادات الذكاء الاصطناعي</h2>
                            <p class="mb-0">إعدادات Manus AI والمساعد الذكي</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Form -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-robot me-2"></i>
                        إعدادات Manus AI
                    </h5>
                </div>
                <div class="card-body">
                    <form id="settingsForm">
                        @csrf
                        
                        <!-- Manus API Key -->
                        <div class="mb-4">
                            <label for="manus_api_key" class="form-label fw-bold">
                                <i class="fas fa-key text-warning me-2"></i>
                                Manus API Key
                            </label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="manus_api_key" 
                                    name="manus_api_key"
                                    placeholder="sk-4-xxxxxxxxxxxxxxxxx"
                                    value="{{ $settings->where('key', 'manus_api_key')->first()->value ?? '' }}"
                                >
                                <button class="btn btn-outline-secondary" type="button" id="toggleApiKey">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-info" type="button" id="testConnection">
                                    <i class="fas fa-plug me-1"></i>
                                    فحص الاتصال
                                </button>
                            </div>
                            <small class="text-muted">
                                احصل على API Key من: 
                                <a href="https://manus.ai/settings/api" target="_blank">https://manus.ai/settings/api</a>
                            </small>
                        </div>

                        <!-- Agent Profile -->
                        <div class="mb-4">
                            <label for="ai_agent_profile" class="form-label fw-bold">
                                <i class="fas fa-brain text-info me-2"></i>
                                نموذج الذكاء الاصطناعي
                            </label>
                            <select class="form-select" id="ai_agent_profile" name="ai_agent_profile">
                                <option value="manus-1.5" {{ ($settings->where('key', 'ai_agent_profile')->first()->value ?? 'manus-1.5') == 'manus-1.5' ? 'selected' : '' }}>
                                    manus-1.5 (الأفضل - موصى به)
                                </option>
                                <option value="manus-1.5-lite" {{ ($settings->where('key', 'ai_agent_profile')->first()->value ?? '') == 'manus-1.5-lite' ? 'selected' : '' }}>
                                    manus-1.5-lite (أسرع)
                                </option>
                            </select>
                            <small class="text-muted">
                                manus-1.5 أكثر ذكاءً ودقة، manus-1.5-lite أسرع في الاستجابة
                            </small>
                        </div>

                        <!-- Task Mode -->
                        <div class="mb-4">
                            <label for="ai_task_mode" class="form-label fw-bold">
                                <i class="fas fa-tasks text-success me-2"></i>
                                وضع المهمة
                            </label>
                            <select class="form-select" id="ai_task_mode" name="ai_task_mode">
                                <option value="chat" {{ ($settings->where('key', 'ai_task_mode')->first()->value ?? 'chat') == 'chat' ? 'selected' : '' }}>
                                    Chat (محادثة عادية)
                                </option>
                                <option value="adaptive" {{ ($settings->where('key', 'ai_task_mode')->first()->value ?? '') == 'adaptive' ? 'selected' : '' }}>
                                    Adaptive (تكيفي)
                                </option>
                                <option value="agent" {{ ($settings->where('key', 'ai_task_mode')->first()->value ?? '') == 'agent' ? 'selected' : '' }}>
                                    Agent (وكيل ذكي)
                                </option>
                            </select>
                            <small class="text-muted">
                                Chat للمحادثات العادية، Agent للمهام المعقدة التي تحتاج أدوات
                            </small>
                        </div>

                        <!-- Save Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>
                                حفظ الإعدادات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Connection Status -->
            <div id="connectionStatus" class="mt-3" style="display: none;"></div>

            <!-- API Info -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات API
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">📌 Endpoint:</h6>
                            <code>https://api.manus.ai/v1/tasks</code>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">📖 التوثيق:</h6>
                            <a href="https://open.manus.ai/docs" target="_blank">
                                https://open.manus.ai/docs
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="fw-bold">🔧 المميزات المتاحة:</h6>
                            <ul>
                                <li>🌐 Browser - تصفح الإنترنت</li>
                                <li>💻 Code Execution - تنفيذ الكود</li>
                                <li>📁 File Access - الوصول للملفات</li>
                                <li>🔍 Search - البحث</li>
                                <li>🇸🇦 دعم كامل للغة العربية</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle API Key visibility
document.getElementById('toggleApiKey').addEventListener('click', function() {
    const input = document.getElementById('manus_api_key');
    const icon = this.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Test Connection
document.getElementById('testConnection').addEventListener('click', async function() {
    const btn = this;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري الفحص...';
    
    const apiKey = document.getElementById('manus_api_key').value;
    
    try {
        const response = await fetch('{{ route("ai.test-connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ api_key: apiKey })
        });
        
        const data = await response.json();
        
        const statusDiv = document.getElementById('connectionStatus');
        statusDiv.style.display = 'block';
        
        if (data.success) {
            statusDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    ${data.message}
                    ${data.task_id ? '<br><small>Task ID: ' + data.task_id + '</small>' : ''}
                </div>
            `;
        } else {
            statusDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i>
                    ${data.message}
                </div>
            `;
        }
    } catch (error) {
        document.getElementById('connectionStatus').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                خطأ في الاتصال: ${error.message}
            </div>
        `;
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
});

// Save Settings
document.getElementById('settingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route("ai.settings.post") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            // إعادة تحميل الصفحة لتحديث القيم
            location.reload();
        } else {
            alert('خطأ: ' + result.message);
        }
    } catch (error) {
        alert('خطأ في الحفظ: ' + error.message);
    }
});
</script>

<style>
.bg-gradient-to-r {
    background: linear-gradient(to right, #9333ea, #ec4899);
}
</style>
@endsection
