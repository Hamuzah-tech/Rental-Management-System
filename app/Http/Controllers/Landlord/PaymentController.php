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
        public function index(Request $request)
    {
        $query = \App\Models\Payment::with([
            'tenant',
            'tenant.property'
        ])->whereHas('tenant.property', function ($q) {
            $q->where('landlord_id', auth()->id());
        });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('landlord.payments.index', compact('payments'));
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