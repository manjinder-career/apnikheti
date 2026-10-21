@extends('layouts.admin')

@section('title', 'Edit Shop Category')

@section('admin_content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Edit Shop Category</h1>
        <a href="{{ route('admin.shop-categories.index') }}" class="text-xs font-semibold text-gray-600 hover:underline">&larr; Back to Categories</a>
    </div>
    <form action="{{ route('admin.shop-categories.update', $category) }}" method="POST" class="bg-white p-6 rounded-xl border shadow-sm">
        @csrf
        @method('PUT')
        @include('admin.shop-categories._form')
    </form>
</div>
@endsection
