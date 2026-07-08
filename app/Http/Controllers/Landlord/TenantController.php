<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\Property;


class TenantController extends Controller
{


    /**
     * Display tenants belonging to logged landlord
     */
    public function index()
    {

        $tenants = Tenant::whereHas(
            'property',
            function ($query) {

                $query->where(
                    'landlord_id',
                    Auth::id()
                );

            }
        )
        ->latest()
        ->paginate(10);



        return view(
            'landlord.tenants.index',
            compact('tenants')
        );

    }





    /**
     * Show create tenant page
     */
    public function create()
    {

        $properties = Property::where(
            'landlord_id',
            Auth::id()
        )
        ->get();



        return view(
            'landlord.tenants.create',
            compact('properties')
        );

    }





    /**
     * Store tenant
     */
    public function store(Request $request)
    {


        $data = $request->validate([


            'property_id' => [
                'required',
                'exists:properties,id'
            ],


            'name' => [
                'required',
                'string'
            ],


            'phone' => [
                'required',
                'string'
            ],


            'email' => [
                'nullable',
                'email'
            ],


            'monthly_rent' => [
                'required',
                'numeric'
            ],


            'move_in_date' => [
                'required',
                'date'
            ],


        ]);





        /*
        |--------------------------------------------------------------------------
        | Make sure landlord owns this property
        |--------------------------------------------------------------------------
        */


        Property::where('id',$data['property_id'])
            ->where('landlord_id',Auth::id())
            ->firstOrFail();







        /*
        |--------------------------------------------------------------------------
        | Generate permanent tenant code
        |--------------------------------------------------------------------------
        */


        do {

            $code = 'TNT-' . strtoupper(
                Str::random(6)
            );


        } while (
            Tenant::where(
                'tenant_code',
                $code
            )->exists()
        );





        $data['tenant_code'] = $code;



        $data['status'] = 'Active';






        Tenant::create($data);





        return redirect()
            ->route('landlord.tenants.index')
            ->with(
                'success',
                'Tenant added successfully. Code: '.$code
            );


    }









    /**
     * Edit tenant
     */
    public function edit(Tenant $tenant)
    {


        $this->checkOwner($tenant);



        $properties = Property::where(
            'landlord_id',
            Auth::id()
        )
        ->get();





        return view(
            'landlord.tenants.edit',
            compact(
                'tenant',
                'properties'
            )
        );


    }









    /**
     * Update tenant
     */
    public function update(
        Request $request,
        Tenant $tenant
    )
    {


        $this->checkOwner($tenant);





        $data = $request->validate([


            'property_id' => [
                'required',
                'exists:properties,id'
            ],


            'name'=>'required|string',


            'phone'=>'required|string',


            'email'=>'nullable|email',


            'monthly_rent'=>'required|numeric',



        ]);






        /*
        Keep tenant_code unchanged
        */



        $tenant->update($data);





        return redirect()
            ->route('landlord.tenants.index')
            ->with(
                'success',
                'Tenant updated successfully.'
            );


    }










    /**
     * Delete tenant
     */
    public function destroy(Tenant $tenant)
    {


        $this->checkOwner($tenant);



        $tenant->delete();




        return back()
            ->with(
                'success',
                'Tenant deleted.'
            );


    }









    /**
     * Move out tenant
     */
    public function moveOut(Tenant $tenant)
    {


        $this->checkOwner($tenant);



        $tenant->update([

            'status'=>'Moved Out'

        ]);




        return back()
            ->with(
                'success',
                'Tenant moved out.'
            );


    }









    /**
     * Reactivate tenant
     */
    public function reactivate(Tenant $tenant)
    {


        $this->checkOwner($tenant);



        $tenant->update([

            'status'=>'Active'

        ]);




        return back()
            ->with(
                'success',
                'Tenant reactivated.'
            );


    }









    /**
     * Security check
     */
    private function checkOwner(Tenant $tenant)
    {


        abort_unless(

            $tenant->property
                ->landlord_id === Auth::id(),

            403

        );


    }



}