@extends('layouts.app')

@section('title', 'Sản phẩm đề xuất | ' . ($siteSettings['site_name'] ?? 'AffiPress'))

@section('content')
    <h1 class="mb-4">Sản phẩm đề xuất</h1>

    <div class="row g-3">
        @forelse($products as $product)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="badge text-bg-light mb-2">{{ $product->affiliate_program }}</span>
                        <h2 class="h5"><a href="{{ route('products.show', $product->slug) }}">{{ $product->title }}</a></h2>
                        <p class="text-muted">{{ $product->description }}</p>
                    </div>
                    <div class="card-footer bg-white d-flex gap-2">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('products.show', $product->slug) }}">Chi tiết</a>
                        <a class="btn btn-sm btn-primary" href="{{ route('affiliate.redirect', $product->slug) }}" target="_blank" rel="nofollow sponsored noopener">Mở link</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Chưa có sản phẩm nào.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $products->links() }}</div>
@endsection
