@extends('layouts.landlord')

@section('title','Add Tenant')

@section('content')

<div class="max-w-3xl mx-auto">

<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

    <!-- Header -->
    <div class="px-6 py-4 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                <x-heroicon-o-user-plus class="w-5 h-5 text-slate-400"/>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    Add Tenant
                </h2>
                <p class="text-sm text-slate-500">
                    Create a new tenant account.
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('landlord.tenants.store') }}">
        @csrf

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Full Name
                </label>
                <input
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Phone Number
                </label>
                <input
                    name="phone"
                    value="{{ old('phone') }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Property
                </label>
                <select
                    name="property_id"
                    id="propertySelect"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}">
                            {{ $property->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Monthly Rent
                </label>
                <input
                    type="number"
                    name="monthly_rent"
                    value="{{ old('monthly_rent') }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Move In Date
                </label>
                <input
                    type="date"
                    name="move_in_date"
                    value="{{ old('move_in_date') }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                    required>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-200 px-6 py-4 flex flex-col sm:flex-row justify-end gap-3">
            <a href="{{ route('landlord.tenants.index') }}"
               class="px-5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition text-center">
                Cancel
            </a>

            <div class="flex flex-col sm:flex-row gap-3">
                <button
                    type="button"
                    id="shareRegistrationBtn"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl transition flex items-center justify-center gap-2">
                    <x-heroicon-o-share class="w-4 h-4"/>
                    Share Registration Link
                </button>

                <button
                    type="submit"
                    class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-xl transition flex items-center justify-center gap-2">
                    <x-heroicon-o-check class="w-4 h-4"/>
                    Save Tenant
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Registration Link Modal -->
<div id="registrationModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" id="modalOverlay"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                        <x-heroicon-o-link class="w-5 h-5 text-blue-600"/>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">
                            Share Registration Link
                        </h3>
                        <p class="text-sm text-slate-500">
                            Generate a registration link for a tenant
                        </p>
                    </div>
                </div>
                <button onclick="closeRegistrationModal()" class="text-slate-400 hover:text-slate-600 transition">
                    <x-heroicon-o-x-mark class="w-6 h-6"/>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5">
                <!-- Property Selection -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Select Property
                    </label>
                    <select
                        id="modalPropertySelect"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Generate Button -->
                <div>
                    <button
                        id="generateLinkBtn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition flex items-center justify-center gap-2">
                        <x-heroicon-o-arrow-path class="w-4 h-4"/>
                        Generate Link
                    </button>
                </div>

                <!-- Loading Indicator -->
                <div id="loadingIndicator" class="hidden">
                    <div class="flex items-center justify-center py-4">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <span class="ml-2 text-slate-600">Generating link...</span>
                    </div>
                </div>

                <!-- Generated Link -->
                <div id="linkContainer" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Registration Link
                    </label>
                    <div class="flex gap-3">
                        <input
                            id="registrationLink"
                            type="text"
                            readonly
                            class="flex-1 rounded-xl border-slate-200 bg-slate-50 text-slate-600 focus:border-slate-400 focus:ring-slate-400 cursor-default">
                        <button
                            id="copyLinkBtn"
                            class="flex-shrink-0 bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl transition flex items-center gap-2">
                            <x-heroicon-o-clipboard class="w-4 h-4"/>
                            Copy
                        </button>
                    </div>
                    <p id="copySuccessMessage" class="text-sm text-green-600 mt-2 hidden">
                        <x-heroicon-o-check-circle class="w-4 h-4 inline-block mr-1"/>
                        Registration link copied successfully.
                    </p>
                </div>

                <!-- Error Message -->
                <div id="errorMessage" class="hidden">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700">
                        <p id="errorText">Failed to generate registration link. Please try again.</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-slate-200 px-6 py-4 flex justify-end">
                <button
                    onclick="closeRegistrationModal()"
                    class="px-5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

</div>

@endsection

@push('scripts')
<script>
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // DOM Elements
    const shareBtn = document.getElementById('shareRegistrationBtn');
    const modal = document.getElementById('registrationModal');
    const overlay = document.getElementById('modalOverlay');
    const closeModalBtn = modal.querySelector('[x-heroicon-o-x-mark]')?.closest('button') || document.querySelector('[onclick="closeRegistrationModal()"]');
    const generateBtn = document.getElementById('generateLinkBtn');
    const copyBtn = document.getElementById('copyLinkBtn');
    const propertySelect = document.getElementById('modalPropertySelect');
    const linkInput = document.getElementById('registrationLink');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const linkContainer = document.getElementById('linkContainer');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const copySuccessMessage = document.getElementById('copySuccessMessage');

    // Open modal
    function openRegistrationModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Reset state
        linkContainer.classList.add('hidden');
        errorMessage.classList.add('hidden');
        copySuccessMessage.classList.add('hidden');
        loadingIndicator.classList.add('hidden');
        linkInput.value = '';
    }

    // Close modal
    function closeRegistrationModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        linkContainer.classList.add('hidden');
        errorMessage.classList.add('hidden');
        copySuccessMessage.classList.add('hidden');
        loadingIndicator.classList.add('hidden');
        linkInput.value = '';
    }

    // Generate link
    function generateRegistrationLink() {
        const propertyId = propertySelect.value;
        
        // Hide previous results
        linkContainer.classList.add('hidden');
        errorMessage.classList.add('hidden');
        copySuccessMessage.classList.add('hidden');
        loadingIndicator.classList.remove('hidden');

        // Make AJAX request
        fetch('{{ route("landlord.tenants.generate-link") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                property_id: propertyId
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            loadingIndicator.classList.add('hidden');
            
            if (data.success) {
                linkInput.value = data.link;
                linkContainer.classList.remove('hidden');
            } else {
                throw new Error(data.message || 'Failed to generate link');
            }
        })
        .catch(error => {
            console.error('Error generating link:', error);
            loadingIndicator.classList.add('hidden');
            errorText.textContent = error.message || 'Failed to generate registration link. Please try again.';
            errorMessage.classList.remove('hidden');
        });
    }

    // Copy link
    function copyRegistrationLink() {
        const link = linkInput.value;

        if (!link) {
            alert('No link to copy. Please generate a link first.');
            return;
        }

        // Use the Clipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(link)
                .then(() => {
                    showCopySuccess();
                })
                .catch(() => {
                    fallbackCopy(link);
                });
        } else {
            fallbackCopy(link);
        }
    }

    function fallbackCopy(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.opacity = '0';
        document.body.appendChild(textArea);
        textArea.select();
        
        try {
            document.execCommand('copy');
            showCopySuccess();
        } catch (err) {
            alert('Failed to copy link. Please select and copy manually.');
        } finally {
            document.body.removeChild(textArea);
        }
    }

    function showCopySuccess() {
        copySuccessMessage.classList.remove('hidden');
        // Auto-hide after 3 seconds
        setTimeout(() => {
            copySuccessMessage.classList.add('hidden');
        }, 3000);
    }

    // Event Listeners
    shareBtn?.addEventListener('click', openRegistrationModal);
    generateBtn?.addEventListener('click', generateRegistrationLink);
    copyBtn?.addEventListener('click', copyRegistrationLink);

    // Close modal on overlay click
    overlay?.addEventListener('click', closeRegistrationModal);

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeRegistrationModal();
        }
    });

    // Auto-generate link when property changes
    propertySelect?.addEventListener('change', function() {
        if (!modal.classList.contains('hidden')) {
            generateRegistrationLink();
        }
    });

    console.log('Registration modal script loaded successfully');
</script>
@endpush