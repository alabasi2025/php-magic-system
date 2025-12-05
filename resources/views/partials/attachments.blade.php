{{--
    ملف: resources/views/journal_entries/partials/attachments.blade.php
    الوصف: قسم إدارة وعرض المرفقات لقيد يومية.
    المتغيرات:
    - $journalEntryId: معرف قيد اليومية (يجب تمريره من View الأب).
    - $isEditable: قيمة منطقية لتحديد ما إذا كان القسم قابلاً للتحرير (رفع/حذف).
--}}

<div id="attachments-section" data-journal-entry-id="{{ $journalEntryId }}">
    <h4 class="mb-3">
        <i class="fa fa-paperclip"></i> المرفقات
        @if ($isEditable)
            <small class="text-muted">(الحد الأقصى 5MB لكل ملف. الأنواع المسموحة: PDF, JPG, PNG, Excel)</small>
        @endif
    </h4>

    {{-- قائمة المرفقات الحالية --}}
    <ul id="attachments-list" class="list-group mb-4">
        {{-- سيتم ملؤها بواسطة JavaScript --}}
        <li class="list-group-item text-center text-muted" id="no-attachments-message">
            لا توجد مرفقات حالياً.
        </li>
    </ul>

    @if ($isEditable)
        {{-- نموذج رفع الملفات --}}
        <form id="attachment-upload-form" enctype="multipart/form-data" class="border p-3 rounded bg-light">
            @csrf
            <div class="form-group">
                <label for="attachments">رفع ملفات جديدة:</label>
                {{-- خاصية multiple تسمح برفع ملفات متعددة --}}
                <input type="file" name="attachments[]" id="attachments" class="form-control-file" multiple>
                <small class="form-text text-danger" id="upload-error-message"></small>
            </div>
            <button type="submit" class="btn btn-primary btn-sm mt-2" id="upload-button">
                <i class="fa fa-upload"></i> رفع الملفات
            </button>
            <div class="progress mt-2 d-none" id="upload-progress">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
        </form>
    @endif

    {{-- جزء JavaScript لإدارة المرفقات (يجب وضعه في نهاية الصفحة) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const section = document.getElementById('attachments-section');
            if (!section) return;

            const journalEntryId = section.dataset.journalEntryId;
            const attachmentsList = document.getElementById('attachments-list');
            const noAttachmentsMessage = document.getElementById('no-attachments-message');
            const isEditable = {{ $isEditable ? 'true' : 'false' }};

            // 1. دالة جلب وعرض المرفقات
            function fetchAttachments() {
                fetch(`/journal-entries/${journalEntryId}/attachments`)
                    .then(response => response.json())
                    .then(attachments => {
                        attachmentsList.innerHTML = '';
                        if (attachments.length === 0) {
                            noAttachmentsMessage.style.display = 'block';
                            attachmentsList.appendChild(noAttachmentsMessage);
                            return;
                        }
                        noAttachmentsMessage.style.display = 'none';

                        attachments.forEach(attachment => {
                            const listItem = createAttachmentListItem(attachment);
                            attachmentsList.appendChild(listItem);
                        });
                    })
                    .catch(error => console.error('Error fetching attachments:', error));
            }

            // 2. دالة إنشاء عنصر القائمة للمرفق
            function createAttachmentListItem(attachment) {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.id = `attachment-${attachment.id}`;

                // أيقونة ونوع الملف
                let icon = 'fa-file';
                if (attachment.is_image) {
                    icon = 'fa-image';
                } else if (attachment.file_type.includes('pdf')) {
                    icon = 'fa-file-pdf';
                } else if (attachment.file_type.includes('excel') || attachment.file_type.includes('spreadsheet')) {
                    icon = 'fa-file-excel';
                }

                let actionsHtml = `
                    <a href="/attachments/${attachment.id}/download" class="btn btn-info btn-sm me-2" title="تحميل">
                        <i class="fa fa-download"></i>
                    </a>
                `;

                // زر الحذف يظهر فقط إذا كان قابلاً للتحرير
                if (isEditable) {
                    actionsHtml += `
                        <button type="button" class="btn btn-danger btn-sm delete-attachment" data-id="${attachment.id}" title="حذف">
                            <i class="fa fa-trash"></i>
                        </button>
                    `;
                }

                // معاينة الصورة تظهر فقط إذا كانت صورة
                let previewHtml = '';
                if (attachment.is_image) {
                    // نفترض أن مسار التخزين العام هو /storage
                    const imageUrl = `/storage/${attachment.file_path.replace('public/', '')}`;
                    previewHtml = `
                        <a href="${imageUrl}" target="_blank" class="btn btn-secondary btn-sm me-2" title="معاينة">
                            <i class="fa fa-eye"></i>
                        </a>
                    `;
                }

                li.innerHTML = `
                    <div>
                        <i class="fa ${icon} me-2"></i>
                        <strong>${attachment.file_name}</strong>
                        <span class="badge bg-secondary ms-2">${attachment.readable_size}</span>
                        <small class="text-muted ms-3">تم الرفع بواسطة: ${attachment.uploader ? attachment.uploader.name : 'غير معروف'}</small>
                    </div>
                    <div>
                        ${previewHtml}
                        ${actionsHtml}
                    </div>
                `;

                return li;
            }

            // 3. دالة حذف المرفق
            if (isEditable) {
                attachmentsList.addEventListener('click', function (e) {
                    if (e.target.closest('.delete-attachment')) {
                        const button = e.target.closest('.delete-attachment');
                        const attachmentId = button.dataset.id;

                        if (!confirm('هل أنت متأكد من حذف هذا المرفق؟')) {
                            return;
                        }

                        fetch(`/attachments/${attachmentId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                // إزالة العنصر من القائمة
                                document.getElementById(`attachment-${attachmentId}`).remove();
                                // إعادة جلب القائمة للتحقق من رسالة "لا توجد مرفقات"
                                fetchAttachments();
                                alert('تم حذف المرفق بنجاح.');
                            } else {
                                return response.json().then(data => { throw new Error(data.message || 'فشل الحذف'); });
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting attachment:', error);
                            alert('فشل في حذف المرفق: ' + error.message);
                        });
                    }
                });

                // 4. دالة رفع الملفات عبر AJAX
                const uploadForm = document.getElementById('attachment-upload-form');
                const uploadButton = document.getElementById('upload-button');
                const uploadProgress = document.getElementById('upload-progress');
                const uploadProgressBar = uploadProgress.querySelector('.progress-bar');
                const uploadErrorMessage = document.getElementById('upload-error-message');

                uploadForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    uploadErrorMessage.textContent = '';
                    uploadButton.disabled = true;
                    uploadProgress.classList.remove('d-none');

                    fetch(`/journal-entries/${journalEntryId}/attachments`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            // لا نحتاج لـ 'Content-Type': 'multipart/form-data' لأن FormData تتولى ذلك
                        },
                        // يمكن إضافة مراقبة التقدم هنا إذا كان المتصفح يدعمها
                    })
                    .then(response => {
                        uploadButton.disabled = false;
                        uploadProgress.classList.add('d-none');
                        uploadProgressBar.style.width = '0%';
                        uploadProgressBar.textContent = '0%';

                        if (response.ok) {
                            return response.json();
                        } else {
                            return response.json().then(data => { throw new Error(data.message || 'فشل الرفع'); });
                        }
                    })
                    .then(data => {
                        alert(data.message);
                        // تحديث قائمة المرفقات
                        fetchAttachments();
                        // مسح حقل الملفات
                        uploadForm.reset();
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                        uploadErrorMessage.textContent = error.message || 'حدث خطأ غير متوقع أثناء الرفع.';
                        uploadButton.disabled = false;
                        uploadProgress.classList.add('d-none');
                    });
                });
            }

            // جلب المرفقات عند تحميل الصفحة
            fetchAttachments();
        });
    </script>
</div>