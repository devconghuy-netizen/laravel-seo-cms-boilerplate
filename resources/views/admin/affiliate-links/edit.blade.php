@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.affiliate-links.index') }}">Quay lại affiliate links</a>
        <h1 class="mt-2 mb-0">Chỉnh sửa affiliate link</h1>
    </div>

    <form method="POST" action="{{ route('admin.affiliate-links.update', $affiliateLink->slug) }}" class="card card-body">
        @csrf
        @method('PUT')
        @include('admin.affiliate-links.form', ['affiliateLink' => $affiliateLink])
    </form>
@endsection
