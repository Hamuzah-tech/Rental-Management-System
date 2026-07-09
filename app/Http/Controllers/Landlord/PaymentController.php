<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display all payments belonging to the logged-in landlord.
     */
    public function index(Request $request)
    {
        $query = Payment::with([
                'tenant.property'
            ])
            ->whereHas('tenant.property', function ($q) {
                $q->where('landlord_id', Auth::id());
            });

        // Optional status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query
            ->latest('submitted_at')
            ->paginate(10);

        return view('landlord.payments.index', compact('payments'));
    }

    /**
     * Show one payment.
     */
    public function show(Payment $payment)
    {
        $this->authorizePayment($payment);

        $payment->load([
            'tenant.property',
            'approver'
        ]);

        return view('landlord.payments.show', compact('payment'));
    }

    /**
     * Approve payment.
     */
    public function approve(Payment $payment)
    {
        $this->authorizePayment($payment);

        $payment->update([

            'status' => 'Approved',

            'remarks' => null,

            'approved_at' => now(),

            'approved_by' => Auth::id(),

        ]);

        return redirect()
            ->route('landlord.payments.show', $payment)
            ->with(
                'success',
                'Payment approved successfully.'
            );
    }

    /**
     * Reject payment.
     */
    public function reject(Request $request, Payment $payment)
    {
        $this->authorizePayment($payment);

        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        $payment->update([

            'status' => 'Rejected',

            'remarks' => $request->remarks,

            'approved_at' => now(),

            'approved_by' => Auth::id(),

        ]);

        return redirect()
            ->route('landlord.payments.show', $payment)
            ->with(
                'success',
                'Payment rejected.'
            );
    }

    /**
     * Ensure payment belongs to landlord.
     */
    private function authorizePayment(Payment $payment)
    {
        abort_unless(

            $payment->tenant
                ->property
                ->landlord_id === Auth::id(),

            403

        );
    }
}