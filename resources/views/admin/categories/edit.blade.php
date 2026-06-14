@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.categories.index') }}">Quay lại danh mục</a>
        <h1 class="mt-2 mb-0">Chỉnh sửa danh mục</h1>
    </div>

    <form method="POST" action="{{ route('admin.categories.update', $category->slug) }}" class="card card-body">
        @csrf
        @method('PUT')
        @include('admin.categories.form', ['category' => $category])
    </form>
@endsection
