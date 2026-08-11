<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alendi Estates</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white relative min-h-screen flex flex-col">
    <!-- Header with Manuals in top-right -->
    <header class="w-full px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-end">
            <div class="relative group">
                <button class="text-[#111827] hover:text-[#ca0251] font-medium text-sm flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-[#F3F4F6] transition">
                    Manuals
                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div class="absolute right-0 mt-2 bg-white rounded-xl border border-[#E5E7EB] shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10 overflow-hidden min-w-[180px]">
                    <button onclick="requestApproval()" 
                            class="w-full text-left px-4 py-3 hover:bg-[#F3F4F6] transition border-b border-[#E5E7EB] text-sm text-[#111827]">
                        Landlord Manual
                    </button>
                    <button onclick="requestTenantApproval()" 
                            class="w-full text-left px-4 py-3 hover:bg-[#F3F4F6] transition text-sm text-[#111827]">
                        Tenant Manual
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">
            
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/alendi_logo.jpg') }}" 
                     alt="Alendi Estates" 
                     class="h-14 w-auto">
            </div>

            <!-- Centered Welcome Section -->
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-[#111827]">
                    Alendi Estates
                </h1>
                <p class="mt-2 text-[#6B7280]">
                    Choose your portal to continue.
                </p>
            </div>

            <div class="mt-10 space-y-4">
                <!-- Landlord -->
                <a href="{{ route('landlord.login') }}"
                   class="flex items-center gap-4 rounded-xl border border-[#E5E7EB] p-5 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:border-[#ca0251]">

                    <x-heroicon-o-home-modern
                        class="w-12 h-12 text-[#6B7280] flex-shrink-0"/>

                    <div class="flex-1">
                        <h2 class="font-semibold text-[#111827]">
                            Landlord
                        </h2>
                        <p class="text-sm text-[#6B7280] mt-0.5">
                            Manage properties, tenants and payments.
                        </p>
                    </div>

                    <x-heroicon-o-arrow-right class="w-5 h-5 text-[#6B7280]"/>

                </a>

                <!-- Tenant Portal -->
                <a href="{{ route('tenant.payments.index') }}"
                   class="flex items-center gap-4 rounded-xl border border-[#E5E7EB] p-5 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:border-[#ca0251]">

                    <x-heroicon-o-credit-card
                        class="w-12 h-12 text-[#6B7280] flex-shrink-0"/>

                    <div class="flex-1">
                        <h2 class="font-semibold text-[#111827]">
                            Tenant
                        </h2>
                        <p class="text-sm text-[#6B7280] mt-0.5">
                            View rent information and payment history.
                        </p>
                    </div>

                    <x-heroicon-o-arrow-right class="w-5 h-5 text-[#6B7280]"/>

                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="w-full py-6 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <p class="text-xs text-[#6B7280]">
                © {{ date('Y') }} Alendi Estates
                <span class="mx-2">·</span>
                <a href="mailto:Hi@alendiestates.com" class="hover:text-[#ca0251] transition">
                    Help
                </a>
            </p>
        </div>
    </footer>

    <!-- Request Approval Modal (Landlord) -->
    <div id="approvalModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6 shadow-xl">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-[#111827] mb-2">Request Access</h3>
                <p class="text-sm text-[#6B7280] mb-6">
                    The Landlord Manual is available upon request.<br>
                    Please contact the Operations Manager for access.
                </p>
                <button onclick="closeModal('approvalModal')" 
                        class="w-full bg-[#0F172A] text-white py-2 px-4 rounded-lg hover:bg-[#ca0251] transition">
                    Okay, Got it
                </button>
            </div>
        </div>
    </div>

    <!-- Tenant Manual Modal -->
    <div id="tenantModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6 shadow-xl">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-[#111827] mb-2">Tenant Manual</h3>
                <p class="text-sm text-[#6B7280] mb-6">
                    The Tenant Manual is available upon request.<br>
                    Please contact the Operations Manager for access.
                </p>
                <button onclick="closeModal('tenantModal')" 
                        class="w-full bg-[#0F172A] text-white py-2 px-4 rounded-lg hover:bg-[#ca0251] transition">
                    Okay, Got it
                </button>
            </div>
        </div>
    </div>

    <style>
        #approvalModal.show, #tenantModal.show {
            display: flex !important;
        }
    </style>

    <script>
        function requestApproval() {
            document.getElementById('approvalModal').classList.add('show');
        }

        function requestTenantApproval() {
            document.getElementById('tenantModal').classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        // Close modals when clicking outside
        document.getElementById('approvalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal('approvalModal');
            }
        });

        document.getElementById('tenantModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal('tenantModal');
            }
        });
    </script>

</body>
</html>