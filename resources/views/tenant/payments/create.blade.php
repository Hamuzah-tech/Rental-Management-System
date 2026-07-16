<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Record Payment · Tenant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white">

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-2xl">

            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('tenant.payments.index') }}"
                   class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition">
                    <x-heroicon-o-arrow-left class="w-5 h-5"/>
                    <span>Back</span>
                </a>
            </div>

            <!-- page header -->
            <h1 class="text-2xl font-bold text-slate-800">Record Payment</h1>
            <p class="mt-1 text-sm text-slate-500">
                Submit your rent payment with proof.
            </p>

            <!-- card – compact version -->
            <div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

                <form method="POST"
                      action="{{ route('tenant.payments.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-4"
                      id="paymentForm">

                    @csrf

                    <!-- tenant code -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Tenant Code
                        </label>
                        <input name="tenant_code" required
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" />
                    </div>

                    <!-- tenant name -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Tenant Name
                        </label>
                        <input name="tenant_name" required
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" />
                    </div>

                    <!-- payment month with icon -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Payment Month
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-calendar class="h-4 w-4 text-slate-400" />
                            </div>
                            <input type="month" name="payment_month" required
                                   class="w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" />
                        </div>
                    </div>

                    <!-- amount -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Amount Paid
                        </label>
                        <input type="number" step="0.01" name="amount" required
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" />
                    </div>

                    <!-- screenshot -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Payment Screenshot
                        </label>
                        <input type="file" name="screenshot" required
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 transition" />
                    </div>

                    <!-- submit button -->
                    <div class="pt-2">
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-[#0F172A] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0F172A] focus:outline-none focus:ring-2 focus:ring-[#0F172A] focus:ring-offset-2">
                            Submit Payment
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <!-- Success Modal -->
    @if(session('success'))
    <div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl transform transition-all scale-100">
            <div class="flex flex-col items-center text-center">
                <!-- Animated Tick -->
                <div class="relative mb-6">
                    <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" 
                                  d="M5 13l4 4L19 7"
                                  class="tick-path" />
                        </svg>
                    </div>
                    <!-- Pulsing ring animation -->
                    <div class="absolute inset-0 rounded-full border-4 border-green-400 animate-ping opacity-75"></div>
                </div>

                <h2 class="text-2xl font-bold text-slate-800 mb-2">Payment Submitted!</h2>
                <p class="text-slate-500 text-sm mb-6">
                    Your payment has been successfully recorded and is pending approval.
                </p>

                <a href="{{ route('tenant.payments.index') }}"
                   class="w-full inline-flex items-center justify-center rounded-lg bg-[#0F172A] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0F172A] focus:outline-none focus:ring-2 focus:ring-[#0F172A] focus:ring-offset-2">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes drawTick {
            0% {
                stroke-dashoffset: 50;
            }
            100% {
                stroke-dashoffset: 0;
            }
        }

        .tick-path {
            stroke-dasharray: 50;
            stroke-dashoffset: 50;
            animation: drawTick 0.6s ease-in-out forwards;
            animation-delay: 0.2s;
        }

        @keyframes ping {
            0% {
                transform: scale(1);
                opacity: 0.75;
            }
            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .animate-ping {
            animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        /* Modal entrance animation */
        #successModal > div {
            animation: modalIn 0.4s ease-out;
        }

        @keyframes modalIn {
            0% {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>

    <script>
        // Auto-close modal after 5 seconds (optional)
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('successModal');
            if (modal) {
                setTimeout(function() {
                    modal.style.opacity = '0';
                    modal.style.transition = 'opacity 0.5s ease';
                    setTimeout(function() {
                        modal.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('successModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.opacity = '0';
                        this.style.transition = 'opacity 0.3s ease';
                        setTimeout(() => {
                            this.style.display = 'none';
                        }, 300);
                    }
                });
            }
        });
    </script>
    @endif

</body>
</html>