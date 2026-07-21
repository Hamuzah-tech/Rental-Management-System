<?php
// app/Http/Controllers/Tenant/PaymentController.php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display payment dashboard for tenants.
     */
    public function index()
    {
        return view('tenant.payments.index');
    }

    /**
     * Show payment form.
     */
    public function create(Request $request)
    {
        $tenant = null;
        $monthlyRent = 0;
        
        // If tenant is logged in
        if (auth()->check() && auth()->user()->role === 'tenant') {
            $tenant = Tenant::where('user_id', auth()->id())->first();
            if ($tenant) {
                $monthlyRent = $tenant->monthly_rent ?? 0;
            }
        }
        
        // If tenant code is provided via session or request
        if (!$tenant && $request->session()->has('tenant_code')) {
            $tenant = Tenant::where('tenant_code', $request->session()->get('tenant_code'))->first();
            if ($tenant) {
                $monthlyRent = $tenant->monthly_rent ?? 0;
            }
        }
        
        return view('tenant.payments.create', compact('monthlyRent', 'tenant'));
    }

    /**
     * Store payment.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'tenant_code' => 'required|string|exists:tenants,tenant_code',
                'tenant_name' => 'required|string|max:255',
                'payment_month' => 'required|string',
                'month_count' => 'nullable|integer|min:1|max:12',
                'amount' => 'required|numeric|min:0',
                'screenshot' => 'required|image|max:2048|mimes:jpeg,png,jpg,pdf',
            ]);

            // Find tenant by code
            $tenant = Tenant::where('tenant_code', $data['tenant_code'])->first();

            if (!$tenant) {
                return back()->withErrors(['tenant_code' => 'Invalid tenant code. Please try again.'])->withInput();
            }

            // Verify tenant name matches
            if (strtolower($tenant->name) !== strtolower($data['tenant_name'])) {
                return back()->withErrors(['tenant_name' => 'Tenant name does not match the tenant code.'])->withInput();
            }

            // Handle multiple months
            $monthCount = $request->month_count ?? 1;
            $baseMonth = $data['payment_month'];

            // Generate months array
            $months = [];
            $currentMonth = \Carbon\Carbon::createFromFormat('Y-m', $baseMonth);
            
            for ($i = 0; $i < $monthCount; $i++) {
                $months[] = $currentMonth->copy()->addMonths($i)->format('Y-m');
            }

            // Store as comma-separated string
            $paymentMonths = implode(',', $months);

            // Handle screenshot upload
            $screenshotPath = null;
            if ($request->hasFile('screenshot')) {
                $file = $request->file('screenshot');
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $screenshotPath = $file->storeAs('payments', $filename, 'public');
            }

            // Create payment record
            $payment = Payment::create([
                'tenant_id' => $tenant->id,
                'payment_month' => $paymentMonths,
                'amount' => $data['amount'],
                'status' => 'Pending',
                'screenshot' => $screenshotPath,
            ]);

            Log::info('Payment recorded successfully', [
                'payment_id' => $payment->id,
                'tenant_id' => $tenant->id,
                'amount' => $payment->amount,
                'months' => $paymentMonths
            ]);

            return redirect()
                ->route('tenant.payments.index')
                ->with('success', 'Payment recorded successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Payment recording failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to record payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Get payment history for a tenant.
     * Shows search form first, then validates and displays results.
     */
    public function history(Request $request)
    {
        $tenant = null;
        $payments = null;
        $error = null;

        try {
            // Check if tenant_code is provided in the request
            if ($request->has('tenant_code') && $request->tenant_code) {
                // Validate the tenant code
                $request->validate([
                    'tenant_code' => 'required|string|exists:tenants,tenant_code',
                ], [
                    'tenant_code.exists' => 'Invalid Tenant Code. Please check and try again.',
                    'tenant_code.required' => 'Please enter your tenant code.',
                ]);

                // Find the tenant
                $tenant = Tenant::where('tenant_code', $request->tenant_code)->first();
                
                if ($tenant) {
                    $payments = Payment::where('tenant_id', $tenant->id)
                        ->latest()
                        ->paginate(10);
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // If validation fails, return with errors
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Payment history error: ' . $e->getMessage());
            $error = 'An error occurred while fetching payment history.';
        }

        // If no tenant code provided, show empty state
        return view('tenant.payments.history', compact('payments', 'tenant', 'error'));
    }

    /**
     * Search for payments by tenant code.
     */
    public function search(Request $request)
    {
        $request->validate([
            'tenant_code' => 'required|string|exists:tenants,tenant_code',
        ]);

        return redirect()->route('tenant.payments.history', ['tenant_code' => $request->tenant_code]);
    }

    /**
     * Public search for payments.
     */
    public function publicSearch(Request $request)
    {
        // Implementation for public payment search
    }

    /**
     * Public payment history by reference.
     */
    public function publicHistory($reference)
    {
        // Implementation for public history
    }
}