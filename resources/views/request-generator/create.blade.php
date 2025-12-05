@extends('layouts.app')

@section('title', 'إنشاء Request جديد')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-0">
                                <i class="fas fa-plus-circle text-primary"></i>
                                إنشاء Form Request جديد
                            </h2>
                            <p class="text-muted mb-0">استخدم الذكاء الاصطناعي لتوليد Form Request احترافي</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('request-generator.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> رجوع
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Form Section -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cog text-primary"></i>
                                إعدادات Request
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="requestForm">
                                @csrf

                                <!-- Request Name -->
                                <div class="mb-3">
                                    <label class="form-label">اسم Request <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="requestName" name="name" 
                                           placeholder="مثال: StoreUserRequest" required>
                                    <small class="text-muted">سيتم إضافة "Request" تلقائياً إذا لم تكن موجودة</small>
                                </div>

                                <!-- Request Type -->
                                <div class="mb-3">
                                    <label class="form-label">نوع Request <span class="text-danger">*</span></label>
                                    <select class="form-select" id="requestType" name="type" required>
                                        @foreach($types as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label class="form-label">الوصف</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="2" placeholder="وصف مختصر للـ Request"></textarea>
                                </div>

                                <!-- Authorization -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="authorization" name="authorization">
                                        <label class="form-check-label" for="authorization">
                                            تفعيل Authorization
                                        </label>
                                    </div>
                                </div>

                                <!-- Authorization Logic -->
                                <div class="mb-3" id="authorizationLogicDiv" style="display: none;">
                                    <label class="form-label">منطق Authorization</label>
                                    <textarea class="form-control" id="authorizationLogic" name="authorization_logic" 
                                              rows="2" placeholder="مثال: Check if user owns the resource"></textarea>
                                </div>

                                <!-- Custom Messages -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="customMessages" 
                                               name="custom_messages" checked>
                                        <label class="form-check-label" for="customMessages">
                                            رسائل خطأ مخصصة
                                        </label>
                                    </div>
                                </div>

                                <hr>

                                <!-- Fields Section -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">الحقول <span class="text-danger">*</span></label>
                                        <button type="button" class="btn btn-sm btn-success" id="addField">
                                            <i class="fas fa-plus"></i> إضافة حقل
                                        </button>
                                    </div>
                                    <div id="fieldsContainer">
                                        <!-- Fields will be added here dynamically -->
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-magic"></i> توليد Request
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="loadTemplate">
                                        <i class="fas fa-layer-group"></i> تحميل من قالب
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-eye text-success"></i>
                                    معاينة الكود
                                </h5>
                                <div>
                                    <button type="button" class="btn btn-sm btn-success" id="saveRequest" disabled>
                                        <i class="fas fa-save"></i> حفظ
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info" id="copyCode" disabled>
                                        <i class="fas fa-copy"></i> نسخ
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="loadingPreview" class="text-center py-5" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">جاري التوليد...</span>
                                </div>
                                <p class="text-muted mt-3">جاري توليد الكود بواسطة AI...</p>
                            </div>
                            <div id="emptyPreview" class="text-center py-5">
                                <i class="fas fa-code fa-3x text-muted mb-3"></i>
                                <p class="text-muted">املأ النموذج وانقر على "توليد Request" لمعاينة الكود</p>
                            </div>
                            <pre id="codePreview" class="mb-0" style="display: none; max-height: 600px; overflow-y: auto;"><code class="language-php"></code></pre>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6><i class="fas fa-info-circle text-info"></i> معلومات مفيدة</h6>
                            <ul class="small mb-0">
                                <li>يمكنك استخدام القوالب الجاهزة للبدء السريع</li>
                                <li>قواعد Validation يمكن دمجها باستخدام | (مثال: required|string|max:255)</li>
                                <li>Authorization يتحقق من صلاحيات المستخدم قبل معالجة الطلب</li>
                                <li>الرسائل المخصصة تحسن تجربة المستخدم</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">اختر قالباً</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($templates as $key => $template)
                    <div class="col-md-6 mb-3">
                        <div class="card border template-card" data-template="{{ $key }}" style="cursor: pointer;">
                            <div class="card-body">
                                <h6 class="card-title">{{ $template['name'] }}</h6>
                                <p class="card-text text-muted small">{{ $template['description'] }}</p>
                                <span class="badge bg-info">{{ $template['type'] }}</span>
                                <span class="badge bg-secondary">{{ count($template['fields']) }} حقول</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">

<script>
let fieldCounter = 0;
let generatedCode = '';
let generatedName = '';

$(document).ready(function() {
    // Add initial field
    addField();

    // Authorization toggle
    $('#authorization').change(function() {
        if ($(this).is(':checked')) {
            $('#authorizationLogicDiv').slideDown();
        } else {
            $('#authorizationLogicDiv').slideUp();
        }
    });

    // Add Field
    $('#addField').click(function() {
        addField();
    });

    // Load Template Button
    $('#loadTemplate').click(function() {
        $('#templateModal').modal('show');
    });

    // Template Selection
    $('.template-card').click(function() {
        const template = $(this).data('template');
        loadTemplate(template);
        $('#templateModal').modal('hide');
    });

    // Form Submit
    $('#requestForm').submit(function(e) {
        e.preventDefault();
        generateRequest();
    });

    // Save Request
    $('#saveRequest').click(function() {
        saveRequest();
    });

    // Copy Code
    $('#copyCode').click(function() {
        copyToClipboard(generatedCode);
    });
});

function addField() {
    fieldCounter++;
    const fieldHtml = `
        <div class="card mb-2 field-item" data-field="${fieldCounter}">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" class="form-control form-control-sm" 
                               name="fields[${fieldCounter}][name]" placeholder="اسم الحقل" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control form-control-sm" 
                               name="fields[${fieldCounter}][rules]" placeholder="قواعد Validation" required>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger remove-field" data-field="${fieldCounter}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#fieldsContainer').append(fieldHtml);

    // Remove field handler
    $(`.remove-field[data-field="${fieldCounter}"]`).click(function() {
        const field = $(this).data('field');
        $(`.field-item[data-field="${field}"]`).remove();
    });
}

function loadTemplate(templateKey) {
    $.ajax({
        url: "{{ route('request-generator.api.templates') }}",
        method: 'GET',
        success: function(response) {
            if (response.success && response.data[templateKey]) {
                const template = response.data[templateKey];
                
                // Fill form
                $('#requestName').val(template.name);
                $('#requestType').val(template.type);
                $('#description').val(template.description);
                $('#authorization').prop('checked', template.authorization);
                $('#customMessages').prop('checked', template.custom_messages);
                
                if (template.authorization) {
                    $('#authorizationLogicDiv').slideDown();
                }

                // Clear and add fields
                $('#fieldsContainer').empty();
                fieldCounter = 0;
                
                template.fields.forEach(field => {
                    fieldCounter++;
                    const rules = Array.isArray(field.rules) ? field.rules.join('|') : field.rules;
                    const fieldHtml = `
                        <div class="card mb-2 field-item" data-field="${fieldCounter}">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control form-control-sm" 
                                               name="fields[${fieldCounter}][name]" value="${field.name}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-sm" 
                                               name="fields[${fieldCounter}][rules]" value="${rules}" required>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-danger remove-field" data-field="${fieldCounter}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#fieldsContainer').append(fieldHtml);
                    
                    $(`.remove-field[data-field="${fieldCounter}"]`).click(function() {
                        const field = $(this).data('field');
                        $(`.field-item[data-field="${field}"]`).remove();
                    });
                });
            }
        }
    });
}

function generateRequest() {
    const formData = $('#requestForm').serializeArray();
    const data = {
        _token: '{{ csrf_token() }}',
        fields: []
    };

    // Parse form data
    formData.forEach(item => {
        if (item.name.startsWith('fields[')) {
            const match = item.name.match(/fields\[(\d+)\]\[(\w+)\]/);
            if (match) {
                const index = match[1];
                const key = match[2];
                
                if (!data.fields[index]) {
                    data.fields[index] = {};
                }
                data.fields[index][key] = item.value;
            }
        } else {
            data[item.name] = item.value;
        }
    });

    // Clean fields array
    data.fields = data.fields.filter(f => f);

    // Convert checkboxes
    data.authorization = $('#authorization').is(':checked');
    data.custom_messages = $('#customMessages').is(':checked');

    // Show loading
    $('#emptyPreview').hide();
    $('#codePreview').hide();
    $('#loadingPreview').show();

    // Generate
    $.ajax({
        url: "{{ route('request-generator.api.generate') }}",
        method: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                generatedCode = response.data.code;
                generatedName = response.data.name;
                
                $('#codePreview code').text(generatedCode);
                Prism.highlightElement($('#codePreview code')[0]);
                
                $('#loadingPreview').hide();
                $('#codePreview').show();
                $('#saveRequest, #copyCode').prop('disabled', false);
            }
        },
        error: function(xhr) {
            $('#loadingPreview').hide();
            $('#emptyPreview').show();
            alert('خطأ في التوليد: ' + (xhr.responseJSON?.error || 'Unknown error'));
        }
    });
}

function saveRequest() {
    if (!generatedCode || !generatedName) {
        alert('لا يوجد كود لحفظه');
        return;
    }

    $.ajax({
        url: "{{ route('request-generator.api.save') }}",
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            name: generatedName,
            code: generatedCode
        },
        success: function(response) {
            if (response.success) {
                alert('تم حفظ Request بنجاح!');
                window.location.href = "{{ route('request-generator.index') }}";
            }
        },
        error: function(xhr) {
            alert('خطأ في الحفظ: ' + (xhr.responseJSON?.error || 'Unknown error'));
        }
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('تم نسخ الكود!');
    });
}
</script>
@endpush
@endsection
