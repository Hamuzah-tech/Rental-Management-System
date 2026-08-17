@extends('layouts.admin')

@section('title', 'Create Landlord')
@section('page-title', 'Create Landlord')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="bg-white rounded-xl shadow">

        <!-- Header -->
        <div class="border-b px-4 sm:px-6 py-4">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">
                Create New Landlord
            </h2>

            <p class="text-gray-500 mt-1">
                Enter the landlord details below.
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.landlords.store') }}">

            @csrf

            <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                <x-form.input
                    label="Full Name"
                    name="name"
                    :value="old('name')"
                />

                <x-form.input
                    label="Username"
                    name="username"
                    :value="old('username')"
                />

                <x-form.input
                    label="Email"
                    name="email"
                    type="email"
                    :value="old('email')"
                />

                <x-form.input
                    label="Phone Number"
                    name="phone"
                    :value="old('phone')"
                />

                <x-form.input
                    label="Second Phone (Optional)"
                    name="second_phone"
                    :value="old('second_phone')"
                />

            </div>

            <!-- Footer -->
            <div class="border-t px-4 sm:px-6 py-4 page-actions">

                <a href="{{ route('admin.landlords.index') }}"
                   class="px-5 py-2 rounded-lg border border-gray-300 hover:bg-gray-100 text-center w-full sm:w-auto">
                    Cancel
                </a>

                <button
                    type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 w-full sm:w-auto">
                    Save Landlord
                </button>

            </div>

        </form>

    </div>

</div>

@endsection