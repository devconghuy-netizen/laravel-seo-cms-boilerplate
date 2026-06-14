@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.tags.index') }}">Quay lại tag</a>
        <h1 class="mt-2 mb-0">Tạo tag</h1>
    </div>

    <form method="POST" action="{{ route('admin.tags.store') }}" class="card card-body">
        @csrf
        @include('admin.tags.form', ['tag' => null])
    </form>
@endsection
