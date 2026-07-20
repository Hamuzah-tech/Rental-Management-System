<!-- resources/views/tenant/registration/show.blade.php -->

@extends('layouts.guest')

@section('title', 'Tenant Registration')

@section('content')
<div class="min-h-screen bg-slate-100 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-200">
                <h1 class="text-2xl font-bold text-slate-800">
                    Register for {{ $property->name }}
                </h1>
                <p class="text-slate-500 mt-1">
                    Fill in your details to register as a tenant.
                </p>
                <p class="text-sm text-slate-400 mt-1">
                    Available slots: {{ $property->availableSlots() }}
                </p>
            </div>

            @if($errors->any())
                <div class="mx-8 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <ul class="text-red-600 text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.registration.store', $property->registration_token) }}" class="p-8 space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                           required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number with Per-Property Uniqueness Check -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="phone" 
                           id="phone"
                           value="{{ old('phone') }}"
                           class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                           placeholder="0977123456"
                           pattern="[0-9]{10,15}"
                           required>
                    <div id="phoneStatus" class="text-xs mt-1"></div>
                    <p class="text-xs text-slate-400 mt-1">
                        ✅ You can use the same phone number if you're moving from another property.
                    </p>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Move In Date -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Move In Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="move_in_date" 
                           value="{{ old('move_in_date', date('Y-m-d')) }}"
                           class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                           required>
                    @error('move_in_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Monthly Rent (Read-only if set on property) -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Monthly Rent (MK)
                    </label>
                    <input type="number" 
                           name="monthly_rent" 
                           value="{{ old('monthly_rent', $property->monthly_rent) }}"
                           class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 bg-slate-50"
                           step="0.01"
                           @if($property->monthly_rent > 0) readonly @endif>
                    @if($property->monthly_rent > 0)
                        <p class="text-xs text-slate-400 mt-1">Fixed rent amount set by landlord</p>
                    @endif
                    @error('monthly_rent')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                        class="w-full bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-xl transition">
                    Register Now
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Real-time phone uniqueness check - PER PROPERTY ONLY
    document.getElementById('phone')?.addEventListener('blur', function() {
        const phone = this.value;
        const statusDiv = document.getElementById('phoneStatus');
        
        if (phone.length >= 10) {
            // Check if phone already exists for THIS property only
            fetch('{{ route('tenant.check-phone') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    phone: phone,
                    property_id: '{{ $property->id }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    statusDiv.innerHTML = '<span class="text-red-500">❌ ' + data.message + '</span>';
                    this.classList.add('border-red-500');
                } else {
                    statusDiv.innerHTML = '<span class="text-green-500">✅ ' + data.message + '</span>';
                    this.classList.remove('border-red-500');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
</script>
@endpush
@endsection