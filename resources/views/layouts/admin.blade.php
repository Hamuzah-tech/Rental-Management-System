<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rental Management System')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <x-admin.sidebar />

    <div class="flex-1 flex flex-col">

        {{-- Topbar --}}
        <x-admin.topbar />

        <main class="flex-1 p-8">
            @yield('content')
        </main>

    </div>

</div>

<!-- Success Modal -->
@if(session('success'))
<div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6 transform transition-all scale-100">
        <div class="flex flex-col items-center text-center">
            <!-- Success Icon -->
            <div class="w-16 h-16 rounded-full bg-[#ca0251]/10 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-[#ca0251]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h2 id="modalTitle" class="text-xl font-bold text-[#111827] mb-2">Success</h2>
            <p class="text-sm text-[#6B7280] mb-6">
                {{ session('success') }}
            </p>

            <button onclick="closeSuccessModal()" 
                    class="w-full inline-flex items-center justify-center rounded-lg bg-[#ca0251] hover:bg-[#a80244] text-white px-6 py-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-[#ca0251] focus:ring-offset-2">
                Continue
            </button>
        </div>
    </div>
</div>

<style>
    #successModal {
        animation: modalFadeIn 0.3s ease-out;
    }
    #successModal > div {
        animation: modalSlideIn 0.3s ease-out;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
</style>

<script>
    (function() {
        'use strict';

        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            if (!modal) return;
            
            modal.style.opacity = '0';
            modal.style.transition = 'opacity 0.3s ease';
            
            setTimeout(function() {
                modal.style.display = 'none';
                // Remove focus trapping if any
                document.body.style.overflow = '';
            }, 300);
        }

        function handleEscapeKey(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('successModal');
                if (modal && modal.style.display !== 'none') {
                    closeSuccessModal();
                }
            }
        }

        function handleOutsideClick(e) {
            const modal = document.getElementById('successModal');
            if (modal && e.target === modal) {
                closeSuccessModal();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const successModal = document.getElementById('successModal');
            if (!successModal) return;

            // Auto-close after 5 seconds
            setTimeout(function() {
                closeSuccessModal();
            }, 5000);

            // Close on outside click
            successModal.addEventListener('click', handleOutsideClick);

            // Close on Escape key
            document.addEventListener('keydown', handleEscapeKey);

            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        });
    })();
</script>
@endif

</body>
</html>