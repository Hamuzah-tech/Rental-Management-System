@extends('layouts.admin')

@section('title','Properties')

@section('page-title','Properties')


@section('content')


<div class="space-y-6">



    <!-- Header -->

    <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 page-header">


        <div>

            <h2 class="text-lg sm:text-xl font-bold text-slate-800">
                Properties
            </h2>


            <p class="text-sm text-slate-500 mt-1">
                Manage all properties.
            </p>


        </div>




        <a href="{{ route('admin.properties.create') }}"
           class="bg-slate-800 text-white px-4 py-2 rounded-xl hover:bg-slate-900 transition text-sm flex items-center justify-center gap-2 w-full sm:w-auto">


            <x-heroicon-o-plus class="w-4 h-4"/>

            Add Property


        </a>



    </div>





    <!-- Table -->






    <!-- Table -->


    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

        {{-- Mobile cards --}}
        <div class="divide-y divide-slate-100 md:hidden">
            @forelse($properties as $index => $property)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-slate-800 truncate">{{ $property->name }}</p>
                            <p class="text-xs text-slate-500">{{ $property->landlord->name }}</p>
                        </div>
                        @if($property->status)
                            <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs flex-shrink-0">Active</span>
                        @else
                            <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-500 text-xs flex-shrink-0">Inactive</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-600">Tenants: {{ $property->tenants_count ?? 0 }}</p>
                    <div class="flex flex-wrap gap-1 pt-1">
                        <a href="{{ route('admin.properties.show',$property) }}" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="View">
                            <x-heroicon-o-eye class="w-5 h-5"/>
                        </a>
                        <a href="{{ route('admin.properties.edit',$property) }}" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit">
                            <x-heroicon-o-pencil-square class="w-5 h-5"/>
                        </a>
                        <form method="POST" action="{{ route('admin.properties.destroy',$property) }}" id="delete-property-mobile-{{ $property->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" title="Delete"
                                onclick="openConfirmModal('delete-property-mobile-{{ $property->id }}','Delete Property','Are you sure you want to delete this property? This action cannot be undone.')"
                                class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                <x-heroicon-o-trash class="w-5 h-5"/>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-slate-500 text-sm">No properties found.</div>
            @endforelse
        </div>

        <div class="hidden md:block table-scroll">
        <table class="w-full text-sm min-w-[640px]">



            <thead class="bg-slate-50">


                <tr class="text-slate-500">


                    <th class="px-4 py-3 text-left">
                        #
                    </th>


                    <th class="px-4 py-3 text-left">
                        Property
                    </th>


                    <th class="px-4 py-3 text-left">
                        Owner
                    </th>


                    <th class="px-4 py-3 text-left">
                        Tenants
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




            @forelse($properties as $index => $property)



            <tr class="border-t border-slate-100 hover:bg-slate-50">



                <td class="px-4 py-3 text-slate-400">

                    {{ $properties->firstItem() + $index }}

                </td>




                <td class="px-4 py-3 font-medium text-slate-700">

                    {{ $property->name }}

                </td>




                <td class="px-4 py-3 text-slate-600">

                    {{ $property->landlord->name }}

                </td>




                <td class="px-4 py-3 text-slate-600">

                    {{ $property->tenants_count ?? 0 }}

                </td>





                <td class="px-4 py-3">


                    @if($property->status)


                    <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">

                        Active

                    </span>


                    @else


                    <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-500 text-xs">

                        Inactive

                    </span>


                    @endif


                </td>






                <td class="px-4 py-3">


                    <div class="flex justify-end gap-1">





                        <!-- View -->


                        <a href="{{ route('admin.properties.show',$property) }}"
                           title="View"
                           class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">


                            <x-heroicon-o-eye class="w-5 h-5"/>


                        </a>






                        <!-- Edit -->


                        <a href="{{ route('admin.properties.edit',$property) }}"
                           title="Edit"
                           class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">


                            <x-heroicon-o-pencil-square class="w-5 h-5"/>


                        </a>







                        <!-- Delete -->


                        <form method="POST"
                              action="{{ route('admin.properties.destroy',$property) }}"
                              id="delete-property-{{ $property->id }}">


                            @csrf
                            @method('DELETE')



                            <button
                                type="button"
                                title="Delete"
                                onclick="openConfirmModal(
                                'delete-property-{{ $property->id }}',
                                'Delete Property',
                                'Are you sure you want to delete this property? This action cannot be undone.'
                                )"
                                class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">



                                <x-heroicon-o-trash class="w-5 h-5"/>



                            </button>



                        </form>





                    </div>


                </td>




            </tr>




            @empty



            <tr>


                <td colspan="6"
                    class="px-6 py-8 text-center text-slate-500">


                    No properties found.


                </td>


            </tr>



            @endforelse




            </tbody>



        </table>
        </div>




    </div>





    <!-- Pagination -->


    <div>

        {{ $properties->links() }}

    </div>




</div>






<!-- Confirmation Modal -->

<div id="confirmModal"
     class="fixed inset-0 hidden items-center justify-center z-50 p-4">



    <div class="absolute inset-0 bg-black/30"
         onclick="closeConfirmModal()">

    </div>





    <div id="modalBox"
         class="relative bg-white rounded-xl border border-slate-200 w-full max-w-md p-6 opacity-0 translate-y-10 transition duration-300">



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





        <div class="page-actions">


            <button onclick="closeConfirmModal()"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 w-full sm:w-auto">

                Cancel

            </button>




            <button onclick="submitConfirmAction()"
                    class="px-4 py-2 rounded-xl bg-slate-800 text-white w-full sm:w-auto">

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



    let modal = document.getElementById('confirmModal');

    let box = document.getElementById('modalBox');



    modal.classList.remove('hidden');

    modal.classList.add('flex');



    setTimeout(()=>{

        box.classList.remove(
            'opacity-0',
            'translate-y-10'
        );

    },50);


}



function closeConfirmModal()
{

    let modal = document.getElementById('confirmModal');

    let box = document.getElementById('modalBox');



    box.classList.add(
        'opacity-0',
        'translate-y-10'
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