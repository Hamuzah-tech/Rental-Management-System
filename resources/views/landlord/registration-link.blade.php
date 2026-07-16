@extends('layouts.landlord')

@section('title', 'Tenant Registration Link')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">

        <!-- Header -->

        <div class="px-6 py-5 border-b border-slate-200">

            <div class="flex items-center gap-4">

                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">

                    <x-heroicon-o-link class="w-6 h-6 text-blue-600"/>

                </div>

                <div>

                    <h2 class="text-2xl font-bold text-slate-800">

                        Tenant Registration Link

                    </h2>

                    <p class="text-slate-500">

                        Share this link with all tenants belonging to this property.

                    </p>

                </div>

            </div>

        </div>

        <!-- Body -->

        <div class="p-6">

            <div class="rounded-xl bg-slate-50 border border-slate-200 p-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            Property

                        </label>

                        <div class="font-semibold text-slate-800">

                            {{ $property->name }}

                        </div>

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            Address

                        </label>

                        <div class="text-slate-700">

                            {{ $property->address }}

                        </div>

                    </div>

                </div>

            </div>

            @php

                $registrationLink = route(
                    'tenant.registration',
                    $property->registration_token
                );

            @endphp

            <div class="mt-8">

                <label class="block text-sm font-semibold text-slate-700 mb-2">

                    Registration Link

                </label>

                <div class="flex gap-3">

                    <input
                        id="registrationLink"
                        type="text"
                        readonly
                        value="{{ $registrationLink }}"
                        class="flex-1 rounded-xl border-slate-300 bg-slate-50">

                    <button
                        onclick="copyLink()"
                        class="bg-slate-800 hover:bg-slate-900 text-white px-6 rounded-xl">

                        Copy

                    </button>

                </div>

            </div>

            <!-- Instructions -->

            <div class="mt-8 rounded-xl border border-blue-200 bg-blue-50 p-5">

                <h3 class="font-semibold text-blue-800">

                    Instructions

                </h3>

                <ul class="mt-3 space-y-2 text-blue-700 text-sm list-disc list-inside">

                    <li>Copy the registration link above.</li>

                    <li>Send it to all your tenants.</li>

                    <li>Tenants will register themselves.</li>

                    <li>Each tenant will automatically receive a unique Tenant Code.</li>

                    <li>Registered tenants will automatically appear in your tenant list.</li>

                </ul>

            </div>

            <!-- Actions -->

            <div class="mt-8 flex flex-wrap gap-3">

                <button
                    onclick="copyLink()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl">

                    Copy Registration Link

                </button>

                <button
                    onclick="window.print()"
                    class="bg-slate-700 hover:bg-slate-800 text-white px-6 py-3 rounded-xl">

                    Print

                </button>

                <a href="{{ route('landlord.tenants.index') }}"
                   class="border border-slate-300 hover:bg-slate-100 px-6 py-3 rounded-xl">

                    Back

                </a>

            </div>

        </div>

    </div>

</div>

<script>

function copyLink()
{
    let copyText = document.getElementById("registrationLink");

    copyText.select();
    copyText.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(copyText.value);

    alert("Registration link copied successfully.");
}

</script>

@endsection