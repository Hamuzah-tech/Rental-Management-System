<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Tenant payment portal home.
     */
    public function index()
    {
        return view('tenant.payments.index');
    }


    /**
     * Show payment submission form.
     */
    public function create()
    {
        return view('tenant.payments.create');
    }


    /**
     * Submit payment.
     */
    public function store(Request $request)
    {
        $data = $request->validate([

            'tenant_code' => [
                'required',
                'string'
            ],

            'tenant_name' => [
                'required',
                'string'
            ],

            'payment_month' => [
                'required'
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1'
            ],

            'screenshot' => [
                'required',
                'image',
                'max:4096'
            ],

        ]);


        $tenant = Tenant::where(
            'tenant_code',
            $data['tenant_code']
        )->first();


        if (! $tenant) {

            return back()
                ->withErrors([
                    'tenant_code' => 'Invalid Tenant Code.'
                ])
                ->withInput();

        }


        if (
            strcasecmp(
                trim($tenant->name),
                trim($data['tenant_name'])
            ) !== 0
        ) {

            return back()
                ->withErrors([
                    'tenant_name' => 'Tenant name does not match the supplied Tenant Code.'
                ])
                ->withInput();

        }


        $exists = Payment::where(
                'tenant_id',
                $tenant->id
            )
            ->where(
                'payment_month',
                $data['payment_month']
            )
            ->exists();


        if ($exists) {

            return back()
                ->withErrors([
                    'payment_month' => 'A payment for this month has already been submitted.'
                ])
                ->withInput();

        }


        $path = $request
            ->file('screenshot')
            ->store('payments', 'public');


        Payment::create([

            'tenant_id'     => $tenant->id,

            'payment_month' => $data['payment_month'],

            'amount'        => $data['amount'],

            'screenshot'    => $path,

            'status'        => 'Pending',

            'submitted_at'  => now(),

        ]);


        return redirect()

            ->route('tenant.payments.index')

            ->with(
                'success',
                'Payment submitted successfully. It is awaiting landlord approval.'
            );
    }


    /**
     * Payment history page.
     */
    public function history()
    {
        return view('tenant.payments.history');
    }


    /**
     * Search tenant payment history.
     */
    public function search(Request $request)
    {
        $request->validate([

            'tenant_code' => [
                'required',
                'string'
            ],

        ]);


        $tenant = Tenant::where(
            'tenant_code',
            $request->tenant_code
        )->first();


        if (! $tenant) {

            return back()
                ->withErrors([
                    'tenant_code' => 'Tenant not found.'
                ]);

        }


        $payments = $tenant
            ->payments()
            ->latest('submitted_at')
            ->get();


        return view(
            'tenant.payments.results',
            compact(
                'tenant',
                'payments'
            )
        );
    }
}