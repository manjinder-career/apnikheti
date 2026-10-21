@extends('layouts.admin')

@section('title', 'Edit Customer — ' . $customer->name)

@section('admin_content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Customer Profile</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ $customer->name }} ({{ $customer->email }})</p>
        </div>
        <a href="{{ route('admin.customers.show', $customer) }}" class="text-xs font-semibold text-gray-600 hover:underline">&larr; Back</a>
    </div>

    <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="bg-white p-6 rounded-xl border shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Customer Name *</label>
                <input type="text" name="name" required value="{{ old('name', $customer->name) }}" class="w-full text-xs p-2.5 rounded-lg border border-gray-300">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Email Address *</label>
                <input type="email" name="email" required value="{{ old('email', $customer->email) }}" class="w-full text-xs p-2.5 rounded-lg border border-gray-300">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" placeholder="+91 98765 43210" class="w-full text-xs p-2.5 rounded-lg border border-gray-300">
            @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Default Shipping Address</label>
            <textarea name="address" rows="2" class="w-full text-xs p-2.5 rounded-lg border border-gray-300">{{ old('address', $customer->address) }}</textarea>
            @error('address') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">City / District</label>
                <input type="text" name="city" value="{{ old('city', $customer->city) }}" class="w-full text-xs p-2.5 rounded-lg border border-gray-300">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">State</label>
                <input type="text" name="state" value="{{ old('state', $customer->state ?? 'Punjab') }}" class="w-full text-xs p-2.5 rounded-lg border border-gray-300">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">PIN Code</label>
                <input type="text" name="pincode" value="{{ old('pincode', $customer->pincode) }}" class="w-full text-xs p-2.5 rounded-lg border border-gray-300">
            </div>
        </div>

        <div class="pt-4 border-t flex justify-end">
            <button type="submit" class="bg-forest-700 hover:bg-forest-800 text-white font-bold py-2.5 px-6 rounded-lg text-xs shadow">
                Save Customer Changes
            </button>
        </div>
    </form>
</div>
@endsection
