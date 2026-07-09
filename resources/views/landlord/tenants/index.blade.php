@extends('layouts.landlord')

@section('title','Tenants')

@section('page-title','Tenants')

@section('content')

<div class="space-y-6">
<!-- Header -->

<div class="bg-white border border-slate-200 rounded-xl p-5 flex justify-between items-center">


    <div>


        <h2 class="text-xl font-bold text-slate-800">

            Tenants

        </h2>


        <p class="text-sm text-slate-500 mt-1">

            Manage your tenants.

        </p>


    </div>




    <a href="{{ route('landlord.tenants.create') }}"
       class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-sm transition flex items-center gap-2">


        <x-heroicon-o-user-plus class="w-4 h-4"/>

        Add Tenant


    </a>



</div>







<!-- Success -->

@if(session('success'))


    <div class="bg-slate-50 border border-slate-200 text-slate-700 px-4 py-3 rounded-xl text-sm">

        {{ session('success') }}

    </div>


@endif







<!-- Table -->


<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">



    <table class="w-full text-sm">



        <thead class="bg-slate-50">


            <tr class="text-slate-500">



                <th class="px-4 py-3 text-left">

                    #

                </th>



                <th class="px-4 py-3 text-left">

                    Tenant Code

                </th>



                <th class="px-4 py-3 text-left">

                    Name

                </th>



                <th class="px-4 py-3 text-left">

                    Phone

                </th>



                <th class="px-4 py-3 text-left">

                    Property

                </th>



                <th class="px-4 py-3 text-left">

                    Rent

                </th>



                <th class="px-4 py-3 text-left">

                    Status

                </th>



                <th class="px-4 py-3 text-right">

                    Actions

                </th>



            </tr>



        </thead>








        <tbody>



        @forelse($tenants as $index => $tenant)



            <tr class="border-t border-slate-100 hover:bg-slate-50 transition">





                <td class="px-4 py-3 text-slate-400">

                    {{ $tenants->firstItem() + $index }}

                </td>







                <td class="px-4 py-3 font-semibold text-slate-700">

                    {{ $tenant->tenant_code }}

                </td>







                <td class="px-4 py-3 text-slate-700">

                    {{ $tenant->name }}

                </td>







                <td class="px-4 py-3 text-slate-600">

                    {{ $tenant->phone }}

                </td>







                <td class="px-4 py-3 text-slate-600">

                    {{ $tenant->property->name ?? 'N/A' }}

                </td>







                <td class="px-4 py-3 text-slate-600">

                    {{ number_format($tenant->monthly_rent) }}

                </td>








                <td class="px-4 py-3">



                    @if($tenant->status == 'Active')


                        <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">

                            Active

                        </span>



                    @else



                        <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-500 text-xs">

                            Moved Out

                        </span>



                    @endif



                </td>







                <td class="px-4 py-3">



                    <div class="flex justify-end gap-1">





                        <!-- Edit -->


                        <a href="{{ route('landlord.tenants.edit',$tenant) }}"
                           title="Edit"
                           class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">


                            <x-heroicon-o-pencil-square class="w-5 h-5"/>


                        </a>








                        @if($tenant->status == 'Active')



                        <!-- Move Out -->


                        <form method="POST"
                              action="{{ route('landlord.tenants.moveout',$tenant) }}"
                              id="moveout-form-{{ $tenant->id }}">


                            @csrf

                            @method('PATCH')



                            <button
                                type="button"
                                title="Move Out"
                                onclick="openConfirmModal(
                                    'moveout-form-{{ $tenant->id }}',
                                    'Move Out Tenant',
                                    'Are you sure you want to move out this tenant?'
                                )"
                                class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">


                                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5"/>


                            </button>


                        </form>






                        @else






                        <!-- Reactivate -->


                        <form method="POST"
                              action="{{ route('landlord.tenants.reactivate',$tenant) }}"
                              id="reactivate-form-{{ $tenant->id }}">


                            @csrf

                            @method('PATCH')



                            <button
                                type="button"
                                title="Reactivate"
                                onclick="openConfirmModal(
                                    'reactivate-form-{{ $tenant->id }}',
                                    'Reactivate Tenant',
                                    'Are you sure you want to reactivate this tenant?'
                                )"
                                class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">


                                <x-heroicon-o-arrow-path class="w-5 h-5"/>


                            </button>


                        </form>





                        @endif
                        <!-- Delete -->


                        <form method="POST"
                              action="{{ route('landlord.tenants.destroy',$tenant) }}"
                              id="delete-form-{{ $tenant->id }}">


                            @csrf

                            @method('DELETE')



                            <button
                                type="button"
                                title="Delete"
                                onclick="openConfirmModal(
                                    'delete-form-{{ $tenant->id }}',
                                    'Delete Tenant',
                                    'Are you sure you want to delete this tenant? This action cannot be undone.'
                                )"
                                class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">


                                <x-heroicon-o-trash class="w-5 h-5"/>


                            </button>


                        </form>






                    </div>




                </td>




            </tr>





        @empty



            <tr>


                <td colspan="8"
                    class="px-6 py-8 text-center text-slate-500">


                    No tenants found.


                </td>


            </tr>



        @endforelse




        </tbody>




    </table>




</div>







<!-- Pagination -->


<div>


    {{ $tenants->links() }}


</div>
</div>

<!-- Confirmation Modal -->

<div id="confirmModal"
     class="fixed inset-0 hidden items-center justify-center z-50">

<!-- Background -->


<div class="absolute inset-0 bg-black/30"
     onclick="closeConfirmModal()">
</div>






<!-- Modal Box -->


<div id="modalBox"
     class="relative bg-white rounded-xl border border-slate-200 w-full max-w-md p-6 transform translate-y-10 opacity-0 transition duration-300">



    <div class="flex items-center gap-3 mb-4">



        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">


            <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-slate-400"/>


        </div>



        <h3 id="modalTitle"
            class="text-lg font-semibold text-slate-800">


        </h3>



    </div>





    <p id="modalMessage"
       class="text-sm text-slate-500 mb-6">


    </p>







    <div class="flex justify-end gap-3">



        <button
            onclick="closeConfirmModal()"
            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">


            Cancel


        </button>





        <button
            onclick="submitConfirmAction()"
            class="px-4 py-2 rounded-xl bg-slate-800 text-white hover:bg-slate-900">


            Confirm


        </button>



    </div>




</div>
</div>

<script>


let selectedForm = null;




function openConfirmModal(formId,title,message)
{


    selectedForm = document.getElementById(formId);



    document.getElementById('modalTitle').innerText = title;


    document.getElementById('modalMessage').innerText = message;




    const modal = document.getElementById('confirmModal');


    const box = document.getElementById('modalBox');




    modal.classList.remove('hidden');


    modal.classList.add('flex');





    setTimeout(()=>{


        box.classList.remove(

            'translate-y-10',

            'opacity-0'

        );



    },50);



}








function closeConfirmModal()
{


    const modal = document.getElementById('confirmModal');


    const box = document.getElementById('modalBox');




    box.classList.add(

        'translate-y-10',

        'opacity-0'

    );





    setTimeout(()=>{


        modal.classList.add('hidden');


        modal.classList.remove('flex');



    },300);



}








function submitConfirmAction()
{


    if(selectedForm)

    {

        selectedForm.submit();

    }


}



</script>

@endsection
