@extends('layouts.admin')

@section('title', 'Landlords')

@section('page-title', 'Landlords')

@section('content')

<div class="space-y-6">


    <!-- Header -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 flex justify-between items-center">

        <div>

            <h2 class="text-xl font-bold text-slate-800">
                Landlords
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Manage all landlords in the system.
            </p>

        </div>


        <a href="{{ route('admin.landlords.create') }}"
           class="bg-slate-800 text-white px-4 py-2 rounded-xl hover:bg-slate-900 transition text-sm">

            + Create Landlord

        </a>

    </div>



    <!-- Notifications -->

    @if(session('success'))

        <div class="bg-slate-50 border border-slate-200 text-slate-700 px-4 py-3 rounded-xl text-sm">

            {{ session('success') }}

        </div>

    @endif



    @if(session('credentials'))

        <div class="bg-slate-50 border border-slate-200 text-slate-700 px-4 py-4 rounded-xl text-sm">


            <h3 class="font-semibold text-slate-800 mb-2">
                Landlord Login Credentials
            </h3>


            <p>
                Username:
                <strong>{{ session('credentials')['username'] }}</strong>
            </p>


            <p>
                Password:
                <strong>{{ session('credentials')['password'] }}</strong>
            </p>


            <p class="text-slate-500 mt-2">
                Save these credentials. The password will not be shown again.
            </p>


        </div>

    @endif



    @if(session('new_password'))

        <div class="bg-slate-50 border border-slate-200 text-slate-700 px-4 py-4 rounded-xl text-sm">

            <h3 class="font-semibold text-slate-800">
                Password Reset Successful
            </h3>


            <p class="mt-2">
                New Password:
                <strong>{{ session('new_password') }}</strong>
            </p>


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
                        Name
                    </th>


                    <th class="px-4 py-3 text-left">
                        Username
                    </th>


                    <th class="px-4 py-3 text-left">
                        Email
                    </th>


                    <th class="px-4 py-3 text-left">
                        Phone
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


            @forelse($landlords as $index => $landlord)


                <tr class="border-t border-slate-100 hover:bg-slate-50">


                    <td class="px-4 py-3 text-slate-400">

                        {{ $landlords->firstItem() + $index }}

                    </td>


                    <td class="px-4 py-3 font-medium text-slate-700">

                        {{ $landlord->name }}

                    </td>


                    <td class="px-4 py-3 text-slate-600">

                        {{ $landlord->username }}

                    </td>


                    <td class="px-4 py-3 text-slate-600">

                        {{ $landlord->email }}

                    </td>


                    <td class="px-4 py-3 text-slate-600">

                        {{ $landlord->phone }}

                    </td>


                    <td class="px-4 py-3">


                        @if($landlord->status)

                            <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">
                                Active
                            </span>

                        @else

                            <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-500 text-xs">
                                Suspended
                            </span>

                        @endif


                    </td>


                    <td class="px-4 py-3">


                        <div class="flex justify-end gap-1">


                            <!-- View -->

                            <a href="{{ route('admin.landlords.show',$landlord) }}"
                               title="View"
                               class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">

                                <x-heroicon-o-eye class="w-5 h-5"/>

                            </a>



                            <!-- Edit -->

                            <a href="{{ route('admin.landlords.edit',$landlord) }}"
                               title="Edit"
                               class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">

                                <x-heroicon-o-pencil-square class="w-5 h-5"/>

                            </a>
                            <!-- Status -->

                            <form action="{{ route('admin.landlords.status',$landlord) }}"
                                  method="POST"
                                  id="status-form-{{ $landlord->id }}">

                                @csrf
                                @method('PATCH')


                                <button
                                    type="button"
                                    title="{{ $landlord->status ? 'Suspend' : 'Activate' }}"
                                    onclick="openConfirmModal(
                                        'status-form-{{ $landlord->id }}',
                                        '{{ $landlord->status ? 'Suspend Landlord' : 'Activate Landlord' }}',
                                        'Are you sure you want to change this landlord status?'
                                    )"
                                    class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">


                                    @if($landlord->status)

                                        <x-heroicon-o-pause-circle class="w-5 h-5"/>

                                    @else

                                        <x-heroicon-o-check-circle class="w-5 h-5"/>

                                    @endif


                                </button>


                            </form>




                            <!-- Reset Password -->

                            <form action="{{ route('admin.landlords.reset-password',$landlord) }}"
                                  method="POST"
                                  id="reset-form-{{ $landlord->id }}">

                                @csrf


                                <button
                                    type="button"
                                    title="Reset Password"
                                    onclick="openConfirmModal(
                                        'reset-form-{{ $landlord->id }}',
                                        'Reset Password',
                                        'Are you sure you want to reset this landlord password?'
                                    )"
                                    class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">


                                    <x-heroicon-o-key class="w-5 h-5"/>


                                </button>


                            </form>





                            <!-- Delete -->

                            <form action="{{ route('admin.landlords.destroy',$landlord) }}"
                                  method="POST"
                                  id="delete-form-{{ $landlord->id }}">

                                @csrf
                                @method('DELETE')


                                <button
                                    type="button"
                                    title="Delete"
                                    onclick="openConfirmModal(
                                        'delete-form-{{ $landlord->id }}',
                                        'Delete Landlord',
                                        'Are you sure you want to delete this landlord? This action cannot be undone.'
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

                    <td colspan="7"
                        class="px-6 py-8 text-center text-slate-500">

                        No landlords found.

                    </td>

                </tr>


            @endforelse


            </tbody>


        </table>


    </div>



    <!-- Pagination -->

    <div>

        {{ $landlords->links() }}

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