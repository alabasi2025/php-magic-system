@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">القيود اليومية</h3>
                    <a href="{{ route('journal-entries.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة قيد جديد
                    </a>
                </div>
                <div class="card-body">
                    @if($entries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>رقم القيد</th>
                                        <th>التاريخ</th>
                                        <th>المرجع</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entries as $entry)
                                        <tr>
                                            <td>{{ $entry->entry_number }}</td>
                                            <td>{{ $entry->entry_date->format('Y-m-d') }}</td>
                                            <td>{{ $entry->reference ?? '-' }}</td>
                                            <td>
                                                @if($entry->status == 'pending')
                                                    <span class="badge badge-warning">معلق</span>
                                                @elseif($entry->status == 'approved')
                                                    <span class="badge badge-success">موافق عليه</span>
                                                @else
                                                    <span class="badge badge-danger">مرفوض</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('journal-entries.show', $entry) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('journal-entries.edit', $entry) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('journal-entries.destroy', $entry) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $entries->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> لا توجد قيود يومية حالياً.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
