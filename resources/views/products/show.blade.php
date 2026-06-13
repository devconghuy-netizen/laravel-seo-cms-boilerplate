@extends('layouts.app')

@section('content')
    <article class="row">
        <div class="col-lg-8">
            <a href="{{ route('products.index') }}">Back to products</a>
            <h1 class="mt-3">{{ $product->title }}</h1>
            <p class="text-muted">{{ $product->affiliate_program }} / {{ $product->type }}</p>
            <p>{{ $product->description }}</p>

            <a class="btn btn-primary" href="{{ $product->url }}" target="_blank" rel="nofollow sponsored noopener">Mo trang san pham</a>
        </div>
    </article>
@endsection
