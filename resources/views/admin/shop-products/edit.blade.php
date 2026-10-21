@extends('layouts.admin')

@section('title', 'Edit Shop Product')

@section('admin_content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Edit Shop Product</h1>
        <a href="{{ route('admin.shop-products.index') }}" class="text-xs font-semibold text-gray-600 hover:underline">&larr; Back to Shop</a>
    </div>
    <form action="{{ route('admin.shop-products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl border shadow-sm">
        @csrf
        @method('PUT')
        @include('admin.shop-products._form')
    </form>
</div>
@endsection
