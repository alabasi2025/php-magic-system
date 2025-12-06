@extends('layouts.app')

@section('title', 'تفاصيل القيد المحاسبي')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تفاصيل القيد المحاسبي</h3>
                    <div class="card-tools">
                        <a href="{{ route('journal-entries.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-list"></i> العودة إلى القائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Success/Error Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Journal Entry Information --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">رقم القيد</span>
                                    <span class="info-box-number">{{ $journalEntry->entry_number }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-calendar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">التاريخ</span>
                                    <span class="info-box-number">{{ $journalEntry->entry_date ? $journalEntry->entry_date->format('Y-m-d') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-info-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">الحالة</span>
                                    <span class="info-box-number">
                                        @if($journalEntry->status == 'draft')
                                            <span class="badge badge-secondary">مسودة</span>
                                        @elseif($journalEntry->status == 'pending')
                                            <span class="badge badge-warning">قيد المراجعة</span>
                                        @elseif($journalEntry->status == 'approved')
                                            <span class="badge badge-success">معتمد</span>
                                        @elseif($journalEntry->status == 'posted')
                                            <span class="badge badge-primary">مرحّل</span>
                                        @elseif($journalEntry->status == 'rejected')
                                            <span class="badge badge-danger">مرفوض</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $journalEntry->status }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-balance-scale"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">التوازن</span>
                                    <span class="info-box-number">
                                        @if($journalEntry->total_debit == $journalEntry->total_credit)
                                            <span class="badge badge-success">متوازن ✓</span>
                                        @else
                                            <span class="badge badge-danger">غير متوازن ✗</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-align-left"></i> الوصف</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $journalEntry->description ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reference & Notes --}}
                    @if($journalEntry->reference || $journalEntry->notes)
                    <div class="row mb-4">
                        @if($journalEntry->reference)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-link"></i> المرجع</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $journalEntry->reference }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($journalEntry->notes)
                        <div class="col-md-{{ $journalEntry->reference ? '6' : '12' }}">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-sticky-note"></i> ملاحظات</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $journalEntry->notes }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Journal Entry Details Table --}}
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="mb-3"><i class="fas fa-table"></i> تفاصيل القيد</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 40%;">الحساب</th>
                                            <th style="width: 20%;">المدين (Debit)</th>
                                            <th style="width: 20%;">الدائن (Credit)</th>
                                            <th style="width: 15%;">الوصف</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($journalEntry->details as $index => $detail)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $detail->account->code }}</strong> - {{ $detail->account->name }}
                                            </td>
                                            <td class="text-right text-success font-weight-bold">
                                                @if($detail->debit > 0)
                                                    {{ number_format($detail->debit, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-right text-danger font-weight-bold">
                                                @if($detail->credit > 0)
                                                    {{ number_format($detail->credit, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $detail->description ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">لا توجد تفاصيل لهذا القيد</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-info font-weight-bold">
                                        <tr>
                                            <td colspan="2" class="text-right">الإجمالي</td>
                                            <td class="text-right text-success">
                                                {{ number_format($journalEntry->details->sum('debit'), 2) }}
                                            </td>
                                            <td class="text-right text-danger">
                                                {{ number_format($journalEntry->details->sum('credit'), 2) }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list"></i> العودة إلى القائمة
                            </a>
                            
                            @if(!in_array($journalEntry->status, ['posted', 'approved']))
                                <a href="{{ route('journal-entries.edit', $journalEntry) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> تعديل القيد
                                </a>
                            @endif

                            @if($journalEntry->status !== 'posted')
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                                    <i class="fas fa-trash"></i> حذف القيد
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> تأكيد الحذف
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0">هل أنت متأكد من حذف القيد المحاسبي <strong>{{ $journalEntry->entry_number }}</strong>؟</p>
                <p class="text-danger mb-0"><strong>تحذير:</strong> هذا الإجراء لا يمكن التراجع عنه!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> إلغاء
                </button>
                <form action="{{ route('journal-entries.destroy', $journalEntry) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> تأكيد الحذف
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
