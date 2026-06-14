@extends('layouts.app')

@section('title', $product->title . ' | ' . ($siteSettings['site_name'] ?? 'AffiPress'))

@section('content')
    <article class="row">
        <div class="col-lg-8">
            <a href="{{ route('products.index') }}">Quay lại danh sách sản phẩm</a>
            <h1 class="mt-3">{{ $product->title }}</h1>
            <p class="text-muted">{{ $product->affiliate_program }} / {{ $product->type }}</p>
            <p>{{ $product->description }}</p>

            <div class="alert alert-light border">
                Link bên dưới có thể là affiliate link. {{ $siteSettings['site_name'] ?? 'AffiPress' }} có thể nhận hoa hồng nếu bạn mua qua liên kết này.
            </div>

            <a class="btn btn-primary" href="{{ route('affiliate.redirect', $product->slug) }}" target="_blank" rel="nofollow sponsored noopener">Mở trang sản phẩm</a>
        </div>
    </article>
@endsection
