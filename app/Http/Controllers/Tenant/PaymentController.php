<?php
// app/Http/Controllers/Tenant/PaymentController.php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
        // Rate limiting: 5 attempts per 10 minutes per IP
        $throttleKey = 'payment_store_' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput()
                ->withErrors(['error' => "Too many attempts. Please try again in {$seconds} seconds."]);
        }

        try {
            $data = $request->validate([
                'tenant_code' => 'required|string|exists:tenants,tenant_code',
                'payment_month' => 'required|string|date_format:Y-m',
                'month_count' => 'nullable|integer|min:1|max:12',
                'amount' => 'required|numeric|min:0',
                'screenshot' => 'required|image|max:102400',
            ]);

            // Find tenant by code
            $tenant = Tenant::where('tenant_code', $data['tenant_code'])->first();

            if (!$tenant) {
                RateLimiter::hit($throttleKey, 600);
                return back()
                    ->withErrors(['tenant_code' => 'Invalid tenant code. Please try again.'])
                    ->withInput();
            }

            // Use tenant name from database - don't trust user input
            $monthCount = $request->month_count ?? 1;
            $baseMonth = $data['payment_month'];

            // Generate months array
            $months = [];
            $currentMonth = Carbon::createFromFormat('Y-m', $baseMonth);
            
            for ($i = 0; $i < $monthCount; $i++) {
                $months[] = $currentMonth->copy()->addMonths($i)->format('Y-m');
            }

            // Calculate expected amount
            $expectedAmount = ($tenant->monthly_rent ?? 0) * $monthCount;
            
            // Validate amount matches expected
            if (round($data['amount'], 2) != round($expectedAmount, 2)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'amount' => "Payment amount should be MK " . number_format($expectedAmount, 2) . 
                                   " for {$monthCount} month(s) at MK " . number_format($tenant->monthly_rent ?? 0, 2) . " per month."
                    ]);
            }

            // Check for duplicate payments for the same months
            foreach ($months as $month) {
                $existingPayment = Payment::where('tenant_id', $tenant->id)
                    ->where(function ($query) use ($month) {
                        $query->where('payment_month', 'LIKE', $month . ',%')
                              ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                              ->orWhere('payment_month', 'LIKE', '%,' . $month)
                              ->orWhere('payment_month', '=', $month);
                    })
                    ->whereIn('status', ['Pending', 'Approved'])
                    ->exists();

                if ($existingPayment) {
                    $monthName = Carbon::createFromFormat('Y-m', $month)->format('F Y');
                    return back()
                        ->withInput()
                        ->withErrors([
                            'payment_month' => "Payment for {$monthName} has already been submitted and is being processed."
                        ]);
                }
            }

            // Store as comma-separated string
            $paymentMonths = implode(',', $months);

            // Handle screenshot upload - FIXED!
            $screenshotPath = null;
            if ($request->hasFile('screenshot')) {
                $file = $request->file('screenshot');
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                // Save directly to public/payments/
                $file->move(public_path('payments'), $filename);
                $screenshotPath = $filename; // Store just the filename
                
                // Log the upload for debugging
                Log::info('Screenshot uploaded', [
                    'filename' => $filename,
                    'path' => public_path('payments/' . $filename),
                    'url' => asset('payments/' . $filename)
                ]);
            }

            // Create payment record
            $payment = Payment::create([
                'tenant_id' => $tenant->id,
                'payment_month' => $paymentMonths,
                'amount' => $data['amount'],
                'status' => 'Pending',
                'screenshot' => $screenshotPath,
            ]);

            // Clear rate limiter on success
            RateLimiter::clear($throttleKey);

            Log::info('Payment recorded successfully', [
                'payment_id' => $payment->id,
                'tenant_id' => $tenant->id,
                'tenant_code' => $tenant->tenant_code,
                'amount' => $payment->amount,
                'months' => $paymentMonths,
                'ip' => $request->ip()
            ]);

            // Store data in session for the success message
            session()->flash('payment_success', true);
            session()->flash('payment_month_count', $monthCount);
            session()->flash('tenant_name', $tenant->name);
            
            return redirect()
                ->route('tenant.payments.create')
                ->with('success', "Payment for {$monthCount} month(s) recorded successfully!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            RateLimiter::hit($throttleKey, 600);
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            RateLimiter::hit($throttleKey, 600);
            
            Log::error('Payment recording failed', [
                'tenant_code' => $request->tenant_code ?? null,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to record payment. Please try again.']);
        }
    }

    /**
     * Get payment history for a tenant.
     * Requires tenant code, rate limited, and uses session verification.
     */
    public function history(Request $request)
    {
        $tenant = null;
        $payments = null;
        $error = null;

        try {
            // Check if tenant_code is provided in the request
            if ($request->has('tenant_code') && $request->tenant_code) {
                // Rate limiting for history lookups
                $throttleKey = 'payment_history_' . $request->ip();
                
                if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
                    $seconds = RateLimiter::availableIn($throttleKey);
                    return back()->withErrors([
                        'error' => "Too many attempts. Please try again in {$seconds} seconds."
                    ]);
                }

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
                    // Store tenant code in session for subsequent requests
                    session(['verified_tenant_code' => $tenant->tenant_code]);
                    
                    $payments = Payment::where('tenant_id', $tenant->id)
                        ->latest()
                        ->paginate(10);
                    
                    RateLimiter::clear($throttleKey);
                }
            } elseif (session()->has('verified_tenant_code')) {
                // If tenant code is in session, use it
                $tenant = Tenant::where('tenant_code', session('verified_tenant_code'))->first();
                if ($tenant) {
                    $payments = Payment::where('tenant_id', $tenant->id)
                        ->latest()
                        ->paginate(10);
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Payment history error', [
                'tenant_code' => $request->tenant_code ?? null,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            $error = 'An error occurred while fetching payment history.';
        }

        return view('tenant.payments.history', compact('payments', 'tenant', 'error'));
    }

    /**
     * Search for payments by tenant code with rate limiting.
     */
    public function search(Request $request)
    {
        // Rate limiting: 5 attempts per 10 minutes per IP
        $throttleKey = 'payment_search_' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'error' => "Too many attempts. Please try again in {$seconds} seconds."
            ]);
        }

        try {
            $request->validate([
                'tenant_code' => 'required|string|exists:tenants,tenant_code',
            ], [
                'tenant_code.exists' => 'Invalid Tenant Code. Please check and try again.',
                'tenant_code.required' => 'Please enter your tenant code.',
            ]);

            // Clear rate limiter on success
            RateLimiter::clear($throttleKey);
            
            // Store verified tenant code in session
            session(['verified_tenant_code' => $request->tenant_code]);

            return redirect()->route('tenant.payments.history', ['tenant_code' => $request->tenant_code]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            RateLimiter::hit($throttleKey, 600);
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            RateLimiter::hit($throttleKey, 600);
            
            Log::error('Payment search error', [
                'tenant_code' => $request->tenant_code ?? null,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            return back()->withErrors(['error' => 'Failed to search for payments. Please try again.']);
        }
    }

    /**
     * Clear the verified tenant session.
     */
    public function clearSession()
    {
        session()->forget('verified_tenant_code');
        return redirect()->route('tenant.payments.history')
            ->with('success', 'Session cleared successfully.');
    }
}