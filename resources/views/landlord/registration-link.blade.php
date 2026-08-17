@extends('layouts.landlord')

@section('title', 'Tenant Registration Link')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">

        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#ca0251]/10 flex items-center justify-center">
                    <x-heroicon-o-link class="w-6 h-6 text-[#ca0251]"/>
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">
                            Property
                        </label>
                        <div class="font-semibold text-slate-800">
                            {{ e($property->name) }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">
                            Address
                        </label>
                        <div class="text-slate-700">
                            {{ e($property->address ?? 'No address provided') }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">
                            Monthly Rent
                        </label>
                        <div class="text-slate-800 font-semibold">
                            MK {{ number_format((float)($property->monthly_rent ?? 0)) }}
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
                <div class="flex flex-col sm:flex-row gap-3 min-w-0">
                    <input
                        id="registrationLink"
                        type="text"
                        readonly
                        value="{{ $registrationLink }}"
                        class="w-full min-w-0 rounded-xl border-slate-300 bg-slate-50 focus:border-[#ca0251] focus:ring-[#ca0251]">
                    <button
                        onclick="copyLink()"
                        class="bg-[#ca0251] hover:bg-[#a80244] text-white px-6 py-2.5 rounded-xl transition w-full sm:w-auto">
                        Copy
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Tenants will see the monthly rent amount of MK {{ number_format((float)($property->monthly_rent ?? 0)) }} when registering
                </p>
            </div>

            <!-- Instructions -->
            <div class="mt-8 rounded-xl border border-[#ca0251]/20 bg-[#ca0251]/5 p-5">
                <h3 class="font-semibold text-[#ca0251]">
                    Instructions
                </h3>
                <ul class="mt-3 space-y-2 text-slate-700 text-sm list-disc list-inside">
                    <li>Copy the registration link above.</li>
                    <li>Send it to all your tenants.</li>
                    <li>Tenants will register themselves.</li>
                    <li>Each tenant will automatically receive a unique Tenant Code.</li>
                    <li>Registered tenants will automatically appear in your tenant list.</li>
                    <li>The monthly rent will be automatically set to <strong>MK {{ number_format((float)($property->monthly_rent ?? 0)) }}</strong> for each tenant.</li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col sm:flex-row flex-wrap gap-3">
                <button
                    onclick="copyLink()"
                    class="bg-[#ca0251] hover:bg-[#a80244] text-white px-6 py-3 rounded-xl transition w-full sm:w-auto">
                    Copy Registration Link
                </button>

                <button
                    onclick="window.print()"
                    class="bg-slate-700 hover:bg-slate-800 text-white px-6 py-3 rounded-xl transition w-full sm:w-auto">
                    Print
                </button>

                <a href="{{ route('landlord.tenants.index') }}"
                   class="border border-slate-300 hover:border-[#ca0251] hover:text-[#ca0251] px-6 py-3 rounded-xl transition w-full sm:w-auto text-center">
                    Back to Tenants
                </a>
            </div>

        </div>

    </div>

</div>

<script>
function copyLink() {
    let copyText = document.getElementById("registrationLink");
    
    if (!copyText) return;
    
    // Select the text
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    
    // Try using the Clipboard API first
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(copyText.value)
            .then(() => {
                // Show success feedback
                showCopySuccess();
            })
            .catch(() => {
                // Fallback to execCommand
                fallbackCopy(copyText.value);
            });
    } else {
        // Fallback for older browsers
        fallbackCopy(copyText.value);
    }
}

function fallbackCopy(text) {
    try {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.opacity = '0';
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showCopySuccess();
    } catch (err) {
        window.notify?.error('Failed to copy link. Please select and copy manually.');
    }
}

function showCopySuccess() {
    // Create a temporary success message
    const copyBtn = document.querySelector('button[onclick="copyLink()"]');
    if (copyBtn) {
        const originalText = copyBtn.textContent;
        copyBtn.textContent = '✓ Copied!';
        copyBtn.classList.add('bg-green-600');
        copyBtn.classList.remove('bg-[#ca0251]', 'hover:bg-[#a80244]');
        
        setTimeout(() => {
            copyBtn.textContent = originalText;
            copyBtn.classList.remove('bg-green-600');
            copyBtn.classList.add('bg-[#ca0251]', 'hover:bg-[#a80244]');
        }, 2000);
    }
}
</script>

@endsection