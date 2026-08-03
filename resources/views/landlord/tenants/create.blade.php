{{-- resources/views/landlord/tenants/create.blade.php --}}
@extends('layouts.landlord')

@section('title','Add Tenant')

@section('content')

<div class="max-w-4xl mx-auto">

<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

    <!-- Header -->
    <div class="px-5 py-3 border-b border-slate-200">
        <div>
            <h2 class="text-base font-semibold text-slate-800">
                Add Tenant
            </h2>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mx-5 mt-3 p-3 bg-[#ca0251]/10 border border-[#ca0251] text-[#ca0251] rounded-lg">
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <!-- General Error Message - Only show for non-field specific errors -->
    @if(session('error') && !$errors->has('property_id'))
        <div class="mx-5 mt-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-red-800 text-sm">Error</h4>
                    <p class="text-red-700 text-sm mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Validation Errors Summary - Show only non-property_id errors here -->
    @if($errors->any() && !$errors->has('property_id'))
        <div class="mx-5 mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-medium text-red-800 text-sm">Please fix the following errors:</p>
                    <ul class="text-red-700 text-sm list-disc list-inside mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('landlord.tenants.store') }}" id="tenantForm" novalidate>
        @csrf

        <div class="p-5 space-y-4">
            <!-- Property Selection - Enhanced with capacity error display -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Hostel <span class="text-red-500">*</span>
                </label>
                <select
                    name="property_id"
                    id="propertySelect"
                    class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] @error('property_id') border-red-500 bg-red-50 @enderror"
                    required>
                    <option value="">Select a hostel</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" 
                                {{ old('property_id') == $property->id ? 'selected' : '' }}>
                            {{ e($property->name) }}
                        </option>
                    @endforeach
                </select>
                
                <!-- Display property_id error only once, with a clean design -->
                @error('property_id')
                    <div class="mt-2 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-red-700 text-sm">{{ $message }}</span>
                        </div>
                    </div>
                @enderror
            </div>

            <!-- Full Name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    maxlength="255"
                    class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] @error('name') border-red-500 bg-red-50 @enderror"
                    required>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number & Email - Side by Side -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Phone Number -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="tel"
                        name="phone"
                        id="phone"
                        value="{{ old('phone') }}"
                        maxlength="15"
                        class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] @error('phone') border-red-500 bg-red-50 @enderror"
                        required>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-500 mt-1">Enter a valid Malawi phone number</p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        maxlength="255"
                        class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] @error('email') border-red-500 bg-red-50 @enderror"
                        required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Move In Date -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Move In Date <span class="text-red-500">*</span>
                </label>
                <input
                    type="date"
                    name="move_in_date"
                    id="move_in_date"
                    value="{{ old('move_in_date', date('Y-m-d')) }}"
                    class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] @error('move_in_date') border-red-500 bg-red-50 @enderror"
                    required>
                @error('move_in_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-200 px-5 py-3 flex flex-col sm:flex-row justify-end gap-2.5">
            <a href="{{ url()->previous() }}"
               class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-[#ca0251]/10 hover:text-[#ca0251] hover:border-[#ca0251] transition text-center text-sm">
                Cancel
            </a>

            <div class="flex flex-col sm:flex-row gap-2.5">
                <button
                    type="button"
                    id="shareRegistrationBtn"
                    class="bg-[#ca0251] hover:bg-[#a80244] text-white px-5 py-2 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.368-2.684 3 3 0 00-5.368 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    Share Registration Link
                </button>

                <button
                    type="submit"
                    id="submitBtn"
                    class="bg-[#ca0251] hover:bg-[#a80244] text-white px-5 py-2 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Add Tenant
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Registration Link Modal -->
<div id="registrationModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" id="modalOverlay"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="px-5 py-3 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Share Registration Link</h3>
                    <p class="text-xs text-slate-500">Generate a registration link for a tenant</p>
                </div>
                <button onclick="closeRegistrationModal()" class="text-slate-400 hover:text-[#ca0251] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Select Hostel</label>
                    <select id="modalPropertySelect" class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251]">
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ e($property->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button
                        id="generateLinkBtn"
                        class="w-full bg-[#ca0251] hover:bg-[#a80244] text-white px-4 py-2 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        Generate Link
                    </button>
                </div>

                <div id="loadingIndicator" class="hidden">
                    <div class="flex items-center justify-center py-4">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#ca0251]"></div>
                        <span class="ml-2 text-slate-600 text-sm">Generating link...</span>
                    </div>
                </div>

                <div id="linkContainer" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Registration Link</label>
                    <div class="flex gap-2.5">
                        <input
                            id="registrationLink"
                            type="text"
                            readonly
                            class="flex-1 rounded-lg border-slate-200 bg-slate-50 text-slate-600 focus:border-[#ca0251] focus:ring-[#ca0251] cursor-default text-sm">
                        <button
                            id="copyLinkBtn"
                            class="flex-shrink-0 bg-[#ca0251] hover:bg-[#a80244] text-white px-4 py-2 rounded-lg transition flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                            Copy
                        </button>
                    </div>
                    <p id="copySuccessMessage" class="text-sm text-[#ca0251] mt-2 hidden">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Registration link copied successfully.
                    </p>
                </div>

                <div id="errorMessage" class="hidden">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm">
                        <p id="errorText">Failed to generate registration link. Please try again.</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200 px-5 py-3 flex justify-end">
                <button onclick="closeRegistrationModal()" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-[#ca0251]/10 hover:text-[#ca0251] hover:border-[#ca0251] transition text-sm">
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Phone input validation - only allow digits and + sign
    document.getElementById('phone')?.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9+]/g, '');
        if (this.value.length > 15) {
            this.value = this.value.slice(0, 15);
        }
    });

    // Modal elements
    const shareBtn = document.getElementById('shareRegistrationBtn');
    const modal = document.getElementById('registrationModal');
    const overlay = document.getElementById('modalOverlay');
    const generateBtn = document.getElementById('generateLinkBtn');
    const copyBtn = document.getElementById('copyLinkBtn');
    const propertySelect = document.getElementById('propertySelect');
    const modalPropertySelect = document.getElementById('modalPropertySelect');
    const linkInput = document.getElementById('registrationLink');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const linkContainer = document.getElementById('linkContainer');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const copySuccessMessage = document.getElementById('copySuccessMessage');

    function openRegistrationModal() {
        const mainPropertyId = propertySelect.value;
        if (mainPropertyId) {
            modalPropertySelect.value = mainPropertyId;
        }
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        linkContainer.classList.add('hidden');
        errorMessage.classList.add('hidden');
        copySuccessMessage.classList.add('hidden');
        loadingIndicator.classList.add('hidden');
        linkInput.value = '';
    }

    function closeRegistrationModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        linkContainer.classList.add('hidden');
        errorMessage.classList.add('hidden');
        copySuccessMessage.classList.add('hidden');
        loadingIndicator.classList.add('hidden');
        linkInput.value = '';
    }

    function generateRegistrationLink() {
        const propertyId = modalPropertySelect.value;
        
        if (!propertyId) {
            errorText.textContent = 'Please select a hostel first.';
            errorMessage.classList.remove('hidden');
            return;
        }
        
        linkContainer.classList.add('hidden');
        errorMessage.classList.add('hidden');
        copySuccessMessage.classList.add('hidden');
        loadingIndicator.classList.remove('hidden');
        generateBtn.disabled = true;

        fetch('{{ route("landlord.tenants.generate-link") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ property_id: propertyId })
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
            generateBtn.disabled = false;
            
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
            generateBtn.disabled = false;
            errorText.textContent = error.message || 'Failed to generate registration link. Please try again.';
            errorMessage.classList.remove('hidden');
        });
    }

    function copyRegistrationLink() {
        const link = linkInput.value;
        if (!link) {
            alert('No link to copy. Please generate a link first.');
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(link)
                .then(() => showCopySuccess())
                .catch(() => fallbackCopy(link));
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
        setTimeout(() => {
            copySuccessMessage.classList.add('hidden');
        }, 3000);
    }

    shareBtn?.addEventListener('click', openRegistrationModal);
    generateBtn?.addEventListener('click', generateRegistrationLink);
    copyBtn?.addEventListener('click', copyRegistrationLink);
    overlay?.addEventListener('click', closeRegistrationModal);

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeRegistrationModal();
        }
    });

    // If there are validation errors, scroll to the first error
    document.addEventListener('DOMContentLoaded', function() {
        const firstError = document.querySelector('.border-red-500');
        if (firstError) {
            firstError.focus();
            setTimeout(() => {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }
    });
</script>
@endpush