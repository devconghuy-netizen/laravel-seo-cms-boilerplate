@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.affiliate-links.index') }}">Quay lại affiliate links</a>
        <h1 class="mt-2 mb-0">Tạo affiliate link</h1>
    </div>

    <form method="POST" action="{{ route('admin.affiliate-links.store') }}" class="card card-body">
        @csrf
        @include('admin.affiliate-links.form', ['affiliateLink' => null])
    </form>
@endsection
