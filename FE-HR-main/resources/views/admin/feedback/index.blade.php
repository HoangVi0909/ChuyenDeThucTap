@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-primary">
                <i class="fas fa-comments me-2"></i>Quản lý Phản hồi
            </h1>
        </div>
        {{-- <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-2"
                    style="background: linear-gradient(90deg, #396afc 0%, #2948ff 100%); color: #fff;">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase mb-1 fw-bold">Tổng phản hồi</div>
                            <div class="h4 mb-0 fw-bold">
                                @isset($feedbacks)
                                {{ is_array($feedbacks) ? count($feedbacks) : 0 }}
                                @else
                                0
                                @endisset
                            </div>
                        </div>
                        <i class="fas fa-comments fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 fw-bold"><i class="fas fa-list me-1"></i>Danh sách phản hồi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    @php
                        $perPage = 5;
                        $currentPageFeedbacks = request()->get('feedbacks_page', 1);
                        $collectionFeedbacks = collect($feedbacks);
                        $paginatedFeedbacks = $collectionFeedbacks->forPage($currentPageFeedbacks, $perPage);
                        $totalPagesFeedbacks = ceil($collectionFeedbacks->count() / $perPage);
                    @endphp

                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Nhân viên</th>
                                <th>Nội dung</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($paginatedFeedbacks->count() > 0)
                                @foreach($paginatedFeedbacks as $feedback)
                                    <tr>
                                        <td class="fw-bold text-primary"><i class="fas fa-user"></i>
                                            {{ $feedback['employee']['name'] ?? ($feedback['employee_id'] ?? 'N/A') }}</td>
                                        <td>
                                            {{ Str::limit($feedback['content'] ?? 'Không có nội dung', 50) }}
                                            <button type="button" class="btn btn-link btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#feedbackModal{{ $feedback['id'] }}">Xem chi tiết</button>

                                            <!-- Modal chi tiết -->
                                            <div class="modal fade" id="feedbackModal{{ $feedback['id'] }}" tabindex="-1"
                                                aria-labelledby="feedbackModalLabel{{ $feedback['id'] }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title" id="feedbackModalLabel{{ $feedback['id'] }}">Chi
                                                                tiết góp ý</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Nhân viên:</strong>
                                                                {{ $feedback['employee']['name'] ?? ($feedback['employee_id'] ?? 'N/A') }}
                                                            </p>
                                                            <p><strong>Nội dung:</strong></p>
                                                            <div class="border rounded p-2 bg-light">
                                                                {{ $feedback['content'] ?? 'Không có nội dung' }}</div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" class="text-center">Không có dữ liệu phản hồi</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    @if($totalPagesFeedbacks > 1)
                    <nav>
                        <ul class="pagination justify-content-center mt-3">
                            @for($i = 1; $i <= $totalPagesFeedbacks; $i++)
                                <li class="page-item {{ $i == $currentPageFeedbacks ? 'active' : '' }}">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['feedbacks_page' => $i]) }}">{{ $i }}</a>
                                </li>
                            @endfor
                        </ul>
                    </nav>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection