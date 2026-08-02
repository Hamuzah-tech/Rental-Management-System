<?php
// app/Http/Controllers/Landlord/PaymentController.php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display all payments for the landlord with filters.
     */
    public function index(Request $request)
    {
        // Start the query
        $query = Payment::whereHas('tenant.property', function ($q) {
            $q->where('landlord_id', Auth::guard('landlord')->id());
        });

        // Filter by property (hostel)
        if ($request->filled('property_id')) {
            $query->whereHas('tenant', function ($q) use ($request) {
                $q->where('property_id', $request->property_id);
            });
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by month if provided
        if ($request->filled('month')) {
            $selectedMonth = $request->month;
            $query->where(function ($q) use ($selectedMonth) {
                $q->where('payment_month', 'LIKE', $selectedMonth . '%')
                  ->orWhere('payment_month', 'LIKE', '%,' . $selectedMonth . '%')
                  ->orWhere('payment_month', 'LIKE', '%,' . $selectedMonth . ',%');
            });
        }

        // Eager load relationships
        $query->with(['tenant' => function ($q) {
            $q->select('id', 'name', 'tenant_code', 'monthly_rent', 'property_id');
        }, 'tenant.property']);

        // Get paginated results
        $payments = $query->latest()->paginate(20);

        // Get properties for filter dropdown
        $properties = Property::where('landlord_id', Auth::guard('landlord')->id())
            ->where('status', true)
            ->get();

        // Generate month options: August 2026 to December 2027
        $months = [];
        $startDate = Carbon::createFromDate(2026, 8, 1);
        $endDate = Carbon::createFromDate(2027, 12, 1);
        
        for ($date = clone $startDate; $date <= $endDate; $date->addMonth()) {
            $key = $date->format('Y-m');
            $months[$key] = $date->format('F Y');
        }

        return view('landlord.payments.index', compact('payments', 'properties', 'months'));
    }

    /**
     * Display a single payment.
     */
    public function show(Payment $payment)
    {
        // Verify payment belongs to the authenticated landlord
        abort_unless(
            $payment->tenant->property->landlord_id === Auth::guard('landlord')->id(),
            403,
            'You are not authorized to view this payment.'
        );

        return view('landlord.payments.show', compact('payment'));
    }

    /**
     * Approve a payment.
     */
    public function approve(Payment $payment)
    {
        // Verify payment belongs to the authenticated landlord
        abort_unless(
            $payment->tenant->property->landlord_id === Auth::guard('landlord')->id(),
            403,
            'You are not authorized to approve this payment.'
        );

        // Check if payment is already processed
        if ($payment->status !== 'Pending') {
            return back()->withErrors([
                'payment' => 'This payment has already been processed.'
            ]);
        }

        // Use database transaction
        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'Approved',
                'approved_by' => Auth::guard('landlord')->id(),
                'approved_at' => now(),
            ]);

            Log::info('Payment approved', [
                'payment_id' => $payment->id,
                'payment_reference' => $payment->reference,
                'approved_by' => Auth::guard('landlord')->id(),
                'tenant_id' => $payment->tenant_id,
                'amount' => $payment->amount
            ]);
        });

        return back()->with('success', 'Payment approved successfully.');
    }

    /**
     * Reject a payment.
     */
    public function reject(Request $request, Payment $payment)
    {
        // Verify payment belongs to the authenticated landlord
        abort_unless(
            $payment->tenant->property->landlord_id === Auth::guard('landlord')->id(),
            403,
            'You are not authorized to reject this payment.'
        );

        // Validate rejection remarks
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if payment is already processed
        if ($payment->status !== 'Pending') {
            return back()->withErrors([
                'payment' => 'This payment has already been processed.'
            ]);
        }

        // Use database transaction
        DB::transaction(function () use ($request, $payment) {
            $payment->update([
                'status' => 'Rejected',
                'remarks' => $request->remarks,
                'approved_by' => Auth::guard('landlord')->id(),
                'approved_at' => now(),
            ]);

            Log::info('Payment rejected', [
                'payment_id' => $payment->id,
                'payment_reference' => $payment->reference,
                'rejected_by' => Auth::guard('landlord')->id(),
                'tenant_id' => $payment->tenant_id,
                'amount' => $payment->amount,
                'remarks' => $request->remarks
            ]);
        });

        return back()->with('success', 'Payment rejected successfully.');
    }
}