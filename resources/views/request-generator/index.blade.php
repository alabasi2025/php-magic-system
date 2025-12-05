@extends('layouts.app')

@section('title', 'Request Generator - مولد Form Requests')

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
                                <i class="fas fa-file-code text-primary"></i>
                                Request Generator
                            </h2>
                            <p class="text-muted mb-0">مولد Form Requests الذكي - v3.29.0</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('request-generator.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إنشاء Request جديد
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($error))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>خطأ:</strong> {{ $error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-0">إجمالي Requests</h6>
                                    <h2 class="text-white mb-0">{{ count($requests) }}</h2>
                                </div>
                                <div class="icon icon-shape bg-white text-primary rounded-circle">
                                    <i class="fas fa-file-code"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-gradient-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-0">القوالب المتاحة</h6>
                                    <h2 class="text-white mb-0">{{ count($templates) }}</h2>
                                </div>
                                <div class="icon icon-shape bg-white text-success rounded-circle">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-gradient-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-0">مدعوم بـ AI</h6>
                                    <h2 class="text-white mb-0"><i class="fas fa-check"></i></h2>
                                </div>
                                <div class="icon icon-shape bg-white text-info rounded-circle">
                                    <i class="fas fa-brain"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-gradient-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-0">الإصدار</h6>
                                    <h2 class="text-white mb-0">3.29.0</h2>
                                </div>
                                <div class="icon icon-shape bg-white text-warning rounded-circle">
                                    <i class="fas fa-code-branch"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Templates -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group text-success"></i>
                        القوالب السريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($templates as $key => $template)
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $template['name'] }}</h6>
                                    <p class="card-text text-muted small">{{ $template['description'] }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-info">{{ $template['type'] }}</span>
                                        <button class="btn btn-sm btn-primary use-template" data-template="{{ $key }}">
                                            <i class="fas fa-magic"></i> استخدام
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Generated Requests Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list text-info"></i>
                        Requests المولدة
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($requests) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="requestsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الحجم</th>
                                    <th>تاريخ التعديل</th>
                                    <th>المسار</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $index => $request)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $request['name'] }}</strong>
                                    </td>
                                    <td>{{ number_format($request['size'] / 1024, 2) }} KB</td>
                                    <td>{{ $request['modified'] }}</td>
                                    <td>
                                        <code class="small">{{ $request['path'] }}</code>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-request" data-path="{{ $request['path'] }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-request" data-name="{{ $request['name'] }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-file-code fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد Requests مولدة بعد</p>
                        <a href="{{ route('request-generator.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> إنشاء أول Request
                        </a>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    @if(count($requests) > 0)
    $('#requestsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json'
        },
        order: [[3, 'desc']]
    });
    @endif

    // Use Template
    $('.use-template').click(function() {
        const template = $(this).data('template');
        window.location.href = "{{ route('request-generator.create') }}?template=" + template;
    });

    // Delete Request
    $('.delete-request').click(function() {
        const name = $(this).data('name');
        
        if (confirm('هل أنت متأكد من حذف هذا Request؟')) {
            $.ajax({
                url: "{{ route('request-generator.api.delete') }}",
                method: 'DELETE',
                data: {
                    name: name,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('فشل الحذف: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('خطأ: ' + xhr.responseJSON.message);
                }
            });
        }
    });
});
</script>
@endpush
@endsection
