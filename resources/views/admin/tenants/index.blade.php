@extends('layouts.admin')

@section('title','Tenants')

@section('page-title','Tenants')


@section('content')


<div class="bg-white border border-slate-200 rounded-xl p-4 mb-6">

    <form method="GET" class="flex flex-col md:flex-row gap-3 md:gap-4">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search tenant..."
            class="flex-1 rounded-xl border-slate-300 w-full">

        <select
            name="landlord"
            onchange="this.form.submit()"
            class="rounded-xl border-slate-300 w-full md:w-auto">

            <option value="">All Landlords</option>

            @foreach($landlords as $landlord)

                <option
                    value="{{ $landlord->id }}"
                    {{ request('landlord') == $landlord->id ? 'selected' : '' }}>

                    {{ $landlord->name }}

                </option>

            @endforeach

        </select>

        <div class="flex flex-col sm:flex-row gap-2">
            <button class="px-5 py-2 bg-slate-800 text-white rounded-xl w-full sm:w-auto">
                Search
            </button>

            <!-- Export Button -->
            <a href="{{ route('admin.tenants.export', request()->query()) }}"
               class="px-5 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2 w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export PDF
            </a>
        </div>

    </form>

</div>


<!-- Table -->


<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

    <div class="divide-y divide-slate-100 md:hidden">
        @forelse($tenants as $index => $tenant)
            <div class="p-4 space-y-2">
                <p class="font-medium text-slate-800">{{ $tenant->name }}</p>
                <p class="text-sm text-slate-600">{{ $tenant->phone }}</p>
                <p class="text-sm text-slate-600">{{ $tenant->property->name ?? 'N/A' }}</p>
                <p class="text-xs text-slate-500">Move in: {{ $tenant->move_in_date }}</p>
                <div class="flex gap-1 pt-1">
                    <a href="{{ route('admin.tenants.edit',$tenant) }}" title="Edit" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                        <x-heroicon-o-pencil-square class="w-5 h-5"/>
                    </a>
                    <form method="POST" action="{{ route('admin.tenants.destroy',$tenant) }}" id="delete-tenant-mobile-{{ $tenant->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" title="Delete"
                            onclick="openConfirmModal('delete-tenant-mobile-{{ $tenant->id }}','Delete Tenant','Are you sure you want to delete this tenant? This action cannot be undone.')"
                            class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                            <x-heroicon-o-trash class="w-5 h-5"/>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-slate-500 text-sm">No tenants found.</div>
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
                    Tenant
                </th>


                <th class="px-4 py-3 text-left">
                    Phone
                </th>


                <th class="px-4 py-3 text-left">
                    Property
                </th>


                <th class="px-4 py-3 text-left">
                    Move In
                </th>


                <th class="px-4 py-3 text-right">
                    Actions
                </th>


            </tr>


        </thead>


        <tbody>



        @forelse($tenants as $index => $tenant)



        <tr class="border-t border-slate-100 hover:bg-slate-50">



            <td class="px-4 py-3 text-slate-400">


                {{ $tenants->firstItem() + $index }}


            </td>




            <td class="px-4 py-3 font-medium text-slate-700">


                {{ $tenant->name }}


            </td>





            <td class="px-4 py-3 text-slate-600">


                {{ $tenant->phone }}


            </td>





            <td class="px-4 py-3 text-slate-600">


                {{ $tenant->property->name ?? 'N/A' }}


            </td>





            <td class="px-4 py-3 text-slate-600">


                {{ $tenant->move_in_date }}


            </td>






            <td class="px-4 py-3">



                <div class="flex justify-end gap-1">





                    <!-- Edit -->


                    <a href="{{ route('admin.tenants.edit',$tenant) }}"
                       title="Edit"
                       class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">


                        <x-heroicon-o-pencil-square class="w-5 h-5"/>


                    </a>






                    <!-- Delete -->


                    <form method="POST"
                          action="{{ route('admin.tenants.destroy',$tenant) }}"
                          id="delete-tenant-{{ $tenant->id }}">


                        @csrf
                        @method('DELETE')



                        <button
                            type="button"
                            title="Delete"
                            onclick="openConfirmModal(
                            'delete-tenant-{{ $tenant->id }}',
                            'Delete Tenant',
                            'Are you sure you want to delete this tenant? This action cannot be undone.'
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


                No tenants found.


            </td>


        </tr>




        @endforelse





        </tbody>




    </table>
    </div>


</div>






<!-- Pagination -->


<div>

    {{ $tenants->links() }}

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


            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">


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