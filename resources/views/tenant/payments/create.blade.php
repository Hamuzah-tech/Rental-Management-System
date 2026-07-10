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
                      class="space-y-4">

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

</body>
</html>