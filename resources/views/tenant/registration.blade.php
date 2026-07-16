@extends('layouts.guest')

@section('content')

<div class="min-h-screen bg-slate-100 py-12">

    <div class="max-w-2xl mx-auto">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            <!-- Header -->

            <div class="px-8 py-6 border-b border-slate-200">

                <div class="flex items-center gap-4">

                    <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">

                        <x-heroicon-o-home class="w-7 h-7 text-blue-600"/>

                    </div>

                    <div>

                        <h1 class="text-2xl font-bold text-slate-800">

                            Tenant Registration

                        </h1>

                        <p class="text-slate-500 mt-1">

                            Complete the form below to register as a tenant.

                        </p>

                    </div>

                </div>

            </div>

            <!-- Property -->

            <div class="px-8 py-5 bg-slate-50 border-b border-slate-200">

                <h2 class="text-lg font-semibold text-slate-700">

                    Property Information

                </h2>

                <div class="mt-3 space-y-2">

                    <p class="text-slate-700">

                        <span class="font-medium">Property:</span>

                        {{ $property->name }}

                    </p>

                    <p class="text-slate-700">

                        <span class="font-medium">Address:</span>

                        {{ $property->address }}

                    </p>

                </div>

            </div>

            <!-- Form -->

            <form method="POST"
                  action="{{ route('tenant.registration.store',$property->registration_token) }}">

                @csrf

                <div class="p-8 space-y-6">

                    @if ($errors->any())

                        <div class="rounded-xl border border-red-200 bg-red-50 p-4">

                            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">

                                @foreach($errors->all() as $error)

                                    <li>{{ $error }}</li>

                                @endforeach

                            </ul>

                        </div>

                    @endif

                    <!-- Name -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">

                            Full Name

                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">

                    </div>

                    <!-- Phone -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">

                            Phone Number

                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">

                    </div>

                    <!-- Email -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">

                            Email Address

                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">

                    </div>

                </div>

                <!-- Footer -->

                <div class="border-t border-slate-200 bg-slate-50 px-8 py-5">

                    <button
                        type="submit"
                        class="w-full bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-xl font-semibold transition">

                        Register

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection