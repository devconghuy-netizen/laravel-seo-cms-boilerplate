@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.tags.index') }}">Quay lại tag</a>
        <h1 class="mt-2 mb-0">Chỉnh sửa tag</h1>
    </div>

    <form method="POST" action="{{ route('admin.tags.update', $tag->slug) }}" class="card card-body">
        @csrf
        @method('PUT')
        @include('admin.tags.form', ['tag' => $tag])
    </form>
@endsection
