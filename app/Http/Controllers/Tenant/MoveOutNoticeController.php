<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\MoveOutNotice;

class MoveOutNoticeController extends Controller
{
    /**
     * Show move-out notice form.
     */
    public function create()
    {
        return view('tenant.moveout.create');
    }

    /**
     * Store move-out notice.
     */
    public function store(Request $request)
    {
        $data = $request->validate([

            'tenant_code' => [
                'required',
                'string'
            ],

            'notice_type' => [
                'required',
                'in:End of this Month,End of Semester,Specific Date,Other'
            ],

            'semester' => [
                'nullable',
                'string'
            ],

            'specific_date' => [
                'nullable',
                'date'
            ],

            'comment' => [
                'nullable',
                'string',
                'max:1000'
            ],

        ]);

        // Find tenant

        $tenant = Tenant::where(
            'tenant_code',
            $data['tenant_code']
        )->first();

        if (!$tenant) {

            return back()
                ->withErrors([
                    'tenant_code' => 'Invalid Tenant Code.'
                ])
                ->withInput();

        }

        // Prevent duplicate pending request

        $pending = MoveOutNotice::where(
            'tenant_id',
            $tenant->id
        )
        ->where(
            'status',
            'Pending'
        )
        ->exists();

        if ($pending) {

            return back()
                ->withErrors([
                    'tenant_code' => 'You already have a pending move-out request.'
                ])
                ->withInput();

        }

        // Validate according to notice type

        if (
            $data['notice_type'] == 'End of Semester'
            && empty($data['semester'])
        ) {

            return back()
                ->withErrors([
                    'semester' => 'Please select a semester.'
                ])
                ->withInput();

        }

        if (
            $data['notice_type'] == 'Specific Date'
            && empty($data['specific_date'])
        ) {

            return back()
                ->withErrors([
                    'specific_date' => 'Please choose a leaving date.'
                ])
                ->withInput();

        }

        MoveOutNotice::create([

            'tenant_id'      => $tenant->id,

            'notice_type'    => $data['notice_type'],

            'semester'       => $data['semester'] ?? null,

            'specific_date'  => $data['specific_date'] ?? null,

            'comment'        => $data['comment'] ?? null,

            'status'         => 'Pending',
        ]);

        return redirect()
            ->route('tenant.moveout.success')
            ->with(
                'success',
                'Your move-out notice has been submitted successfully.'
            );
    }

    /**
     * Success page.
     */
    public function success()
    {
        return view('tenant.moveout.success');
    }
}