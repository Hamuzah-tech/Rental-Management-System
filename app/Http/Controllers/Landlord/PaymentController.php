<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display all payments for the landlord.
     */
public function index()
{
    $payments = Payment::whereHas('tenant.property', function ($q) {
        $q->where('landlord_id', Auth::id());
    })
    ->with(['tenant' => function ($q) {
        $q->select('id', 'name', 'tenant_code', 'monthly_rent', 'property_id');
    }])
    ->latest()
    ->paginate(20);

    return view('landlord.payments.index', compact('payments'));
}

public function store(Request $request)
{
    $data = $request->validate([
        'tenant_id' => 'required|exists:tenants,id',
        'amount' => 'required|numeric|min:0',
        'month' => 'required|date_format:Y-m',
        'reference' => 'nullable|string|max:50',
    ]);

    $tenant = Tenant::findOrFail($data['tenant_id']);
    
    // Use tenant's monthly rent as default
    if (empty($data['amount'])) {
        $data['amount'] = $tenant->monthly_rent ?? 0;
    }

    $payment = Payment::create($data);

    return redirect()
        ->route('landlord.payments.index')
        ->with('success', 'Payment recorded successfully.');
}

    /**
     * Display a single payment.
     */
    public function show(Payment $payment)
    {
        return view('landlord.payments.show', compact('payment'));
    }

    /**
     * Approve a payment.
     */
    public function approve(Payment $payment)
    {
        $payment->update([
            'status' => 'Approved',
        ]);

        return back()->with(
            'success',
            'Payment approved successfully.'
        );
    }

    /**
     * Reject a payment.
     */
    public function reject(Payment $payment)
    {
        $payment->update([
            'status' => 'Rejected',
        ]);

        return back()->with(
            'success',
            'Payment rejected successfully.'
        );
    }
}