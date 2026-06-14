@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Quản lý affiliate links</h1>
            <div class="d-flex gap-3">
                <a href="{{ route('admin.posts.index') }}">Bài viết</a>
                <a href="{{ route('admin.categories.index') }}">Danh mục</a>
                <a href="{{ route('admin.tags.index') }}">Tag</a>
                <a href="{{ route('admin.media.index') }}">Media</a>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.affiliate-links.export') }}">Export CSV</a>
            <a class="btn btn-primary" href="{{ route('admin.affiliate-links.create') }}">Tạo affiliate link</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.affiliate-links.import') }}" enctype="multipart/form-data" class="card card-body mb-4">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-lg-8">
                <label class="form-label" for="csv_file">Import affiliate links từ CSV</label>
                <input class="form-control @error('csv_file') is-invalid @enderror" id="csv_file" name="csv_file" type="file" accept=".csv,text/csv">
                @error('csv_file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Header mẫu: title,url,affiliate_program,type,slug,description,product_id,commission_rate,is_active</div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <button class="btn btn-outline-primary" type="submit">Import CSV</button>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Link đang bật</div>
                    <div class="fs-3 fw-semibold">{{ $stats['active_links'] }}/{{ $stats['total_links'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Tổng click</div>
                    <div class="fs-3 fw-semibold">{{ number_format($stats['total_clicks']) }}</div>
                    <div class="text-muted small">7 ngày: {{ number_format($stats['clicks_7_days']) }} · 30 ngày: {{ number_format($stats['clicks_30_days']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Conversion</div>
                    <div class="fs-3 fw-semibold">{{ number_format($stats['total_conversions']) }}</div>
                    <div class="text-muted small">7 ngày: {{ number_format($stats['conversions_7_days']) }} · 30 ngày: {{ number_format($stats['conversions_30_days']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Tỷ lệ chuyển đổi</div>
                    <div class="fs-3 fw-semibold">{{ $stats['average_conversion_rate'] }}%</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Earning ước tính</div>
                    <div class="fs-3 fw-semibold">${{ number_format($stats['total_earnings'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white fw-semibold">Click 7 ngày gần nhất</div>
        <div class="card-body">
            <div class="row g-2 text-center">
                @foreach($dailyClicks as $day)
                    <div class="col">
                        <div class="border rounded py-2 h-100">
                            <div class="fw-semibold">{{ number_format($day['count']) }}</div>
                            <div class="text-muted small">{{ $day['label'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white fw-semibold">Hiệu suất program 30 ngày</div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Click</th>
                                <th>Conversion</th>
                                <th>CR</th>
                                <th>Earning</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programReport as $program)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $program['program'] }}</div>
                                        <div class="text-muted small">{{ number_format($program['links']) }} link</div>
                                    </td>
                                    <td>{{ number_format($program['clicks']) }}</td>
                                    <td>{{ number_format($program['conversions']) }}</td>
                                    <td>{{ $program['conversion_rate'] }}%</td>
                                    <td>${{ number_format($program['earnings'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td class="text-muted" colspan="5">Chưa có dữ liệu program trong 30 ngày.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white fw-semibold">Top link 30 ngày</div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Link</th>
                                <th>Click</th>
                                <th>Conversion</th>
                                <th>Earning</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($linkReport as $reportLink)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $reportLink->title }}</div>
                                        <div class="text-muted small">{{ $reportLink->affiliate_program }}</div>
                                    </td>
                                    <td>{{ number_format($reportLink->report_clicks) }}</td>
                                    <td>{{ number_format($reportLink->report_conversions) }}</td>
                                    <td>${{ number_format((float) $reportLink->report_earnings, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td class="text-muted" colspan="4">Chưa có dữ liệu link trong 30 ngày.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white fw-semibold">Top affiliate links</div>
        <div class="list-group list-group-flush">
            @forelse($topLinks as $topLink)
                <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <div class="fw-semibold">{{ $topLink->title }}</div>
                        <div class="text-muted small">{{ $topLink->post?->slug ?? 'Không gắn bài viết' }}</div>
                    </div>
                    <div class="text-end small">
                        <div>{{ number_format($topLink->clicks) }} click</div>
                        <div>{{ number_format($topLink->conversions) }} conversion · {{ $topLink->conversion_rate }}%</div>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-muted">Chưa có dữ liệu affiliate.</div>
            @endforelse
        </div>
    </div>

    <form method="GET" action="{{ route('admin.affiliate-links.index') }}" class="card card-body mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label" for="q">Tìm kiếm</label>
                <input class="form-control" id="q" name="q" value="{{ $filters['q'] }}" placeholder="Tiêu đề, slug, program hoặc product ID">
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label" for="program">Program</label>
                <select class="form-select" id="program" name="program">
                    <option value="">Tất cả</option>
                    @foreach($programs as $program)
                        <option value="{{ $program }}" @selected($filters['program'] === $program)>{{ $program }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label" for="type">Loại</label>
                <select class="form-select" id="type" name="type">
                    <option value="">Tất cả</option>
                    <option value="product" @selected($filters['type'] === 'product')>Product</option>
                    <option value="service" @selected($filters['type'] === 'service')>Service</option>
                    <option value="offer" @selected($filters['type'] === 'offer')>Offer</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label" for="status">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tất cả</option>
                    <option value="active" @selected($filters['status'] === 'active')>Đang bật</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Đang tắt</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label" for="sort">Sắp xếp</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="latest" @selected($filters['sort'] === 'latest')>Mới nhất</option>
                    <option value="oldest" @selected($filters['sort'] === 'oldest')>Cũ nhất</option>
                    <option value="clicks" @selected($filters['sort'] === 'clicks')>Nhiều click</option>
                    <option value="conversions" @selected($filters['sort'] === 'conversions')>Nhiều conversion</option>
                    <option value="earnings" @selected($filters['sort'] === 'earnings')>Earning cao</option>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">Tìm thấy {{ number_format($links->total()) }} affiliate link.</div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.affiliate-links.index') }}">Xóa lọc</a>
                <button class="btn btn-primary" type="submit">Áp dụng</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Program</th>
                        <th>Loại</th>
                        <th>Click</th>
                        <th>Conversion</th>
                        <th>Earning</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($links as $link)
                        <tr>
                            <td>
                                <div>{{ $link->title }}</div>
                                <div class="text-muted small">{{ $link->post?->slug ?? 'Không gắn bài viết' }}</div>
                            </td>
                            <td>{{ $link->affiliate_program }}</td>
                            <td>{{ $link->type }}</td>
                            <td>{{ number_format($link->clicks) }}</td>
                            <td>{{ number_format($link->conversions) }} ({{ $link->conversion_rate }}%)</td>
                            <td>${{ number_format($link->earnings, 2) }}</td>
                            <td>
                                <span class="badge text-bg-{{ $link->is_active ? 'success' : 'secondary' }}">
                                    {{ $link->is_active ? 'Đang bật' : 'Đang tắt' }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($link->is_active)
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('products.show', $link->slug) }}">Xem</a>
                                    <form method="POST" action="{{ route('admin.affiliate-links.conversion', $link->slug) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success" type="submit">+ Conversion</button>
                                    </form>
                                @endif
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.affiliate-links.edit', $link->slug) }}">Sửa</a>
                                <form method="POST" action="{{ route('admin.affiliate-links.destroy', $link->slug) }}" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa affiliate link này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-muted" colspan="8">Không tìm thấy affiliate link phù hợp.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $links->links() }}</div>
@endsection
