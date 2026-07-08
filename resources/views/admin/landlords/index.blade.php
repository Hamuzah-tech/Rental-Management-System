@extends('layouts.admin')

@section('title', 'Landlords')

@section('page-title', 'Landlords')

@section('content')

<div class="space-y-6">


    <!-- Header -->
    <div class="bg-white rounded-xl shadow p-6 flex justify-between items-center">

        <div>

            <h2 class="text-2xl font-bold text-gray-800">
                Landlords
            </h2>

            <p class="text-gray-500 mt-1">
                Manage all landlords in the system.
            </p>

        </div>


        <a href="{{ route('admin.landlords.create') }}"
           class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">

            + Create Landlord

        </a>

    </div>



    <!-- Success Message -->
    @if(session('success'))

        <div class="bg-green-100 border border-green-300 text-green-800 px-5 py-3 rounded-lg">

            {{ session('success') }}

        </div>

    @endif



    <!-- New Landlord Credentials -->
    @if(session('credentials'))

        <div class="bg-blue-100 border border-blue-300 text-blue-900 px-5 py-4 rounded-lg">


            <h3 class="font-bold mb-2">
                Landlord Login Credentials
            </h3>


            <p>
                Username:

                <strong>
                    {{ session('credentials')['username'] }}
                </strong>

            </p>


            <p>
                Password:

                <strong>
                    {{ session('credentials')['password'] }}
                </strong>

            </p>


            <p class="text-sm mt-2">
                Save these credentials. The password will not be shown again.
            </p>


        </div>

    @endif



    <!-- Password Reset Message -->
    @if(session('new_password'))

        <div class="bg-purple-100 border border-purple-300 text-purple-900 px-5 py-4 rounded-lg">


            <h3 class="font-bold">
                Password Reset Successful
            </h3>


            <p class="mt-2">

                New Password:

                <strong>
                    {{ session('new_password') }}
                </strong>

            </p>


        </div>

    @endif





    <!-- Table -->

    <div class="bg-white rounded-xl shadow overflow-hidden">


        <table class="w-full">


            <thead class="bg-gray-100">


                <tr>


                    <th class="px-6 py-4 text-left">
                        Name
                    </th>


                    <th class="px-6 py-4 text-left">
                        Username
                    </th>


                    <th class="px-6 py-4 text-left">
                        Email
                    </th>


                    <th class="px-6 py-4 text-left">
                        Phone
                    </th>


                    <th class="px-6 py-4 text-left">
                        Status
                    </th>


                    <th class="px-6 py-4 text-right">
                        Actions
                    </th>


                </tr>


            </thead>



            <tbody>


            @forelse($landlords as $landlord)


                <tr class="border-t">


                    <td class="px-6 py-4">

                        {{ $landlord->name }}

                    </td>



                    <td class="px-6 py-4">

                        {{ $landlord->username }}

                    </td>



                    <td class="px-6 py-4">

                        {{ $landlord->email }}

                    </td>



                    <td class="px-6 py-4">

                        {{ $landlord->phone }}

                    </td>



                    <td class="px-6 py-4">


                        @if($landlord->status)

                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">

                                Active

                            </span>


                        @else


                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm">

                                Suspended

                            </span>


                        @endif


                    </td>




                    <td class="px-6 py-4 text-right">


                        <div class="flex justify-end gap-3 flex-wrap">



                            <!-- View -->

                            <a href="{{ route('admin.landlords.show',$landlord) }}"
                               class="text-blue-600 hover:underline">

                                View

                            </a>




                            <!-- Edit -->

                            <a href="{{ route('admin.landlords.edit',$landlord) }}"
                               class="text-indigo-600 hover:underline">

                                Edit

                            </a>





                            <!-- Status Toggle -->

                            <form action="{{ route('admin.landlords.status',$landlord) }}"
                                  method="POST"
                                  class="inline">


                                @csrf

                                @method('PATCH')


                                <button class="text-yellow-600 hover:underline">


                                    @if($landlord->status)

                                        Suspend

                                    @else

                                        Activate

                                    @endif


                                </button>


                            </form>





                            <!-- Reset Password -->

                            <form action="{{ route('admin.landlords.reset-password',$landlord) }}"
                                  method="POST"
                                  class="inline">


                                @csrf


                                <button
                                    onclick="return confirm('Reset password for this landlord?')"
                                    class="text-purple-600 hover:underline">


                                    Reset Password


                                </button>


                            </form>





                            <!-- Delete -->

                            <form action="{{ route('admin.landlords.destroy',$landlord) }}"
                                  method="POST"
                                  class="inline">


                                @csrf

                                @method('DELETE')



                                <button
                                    onclick="return confirm('Delete this landlord?')"
                                    class="text-red-600 hover:underline">


                                    Delete


                                </button>


                            </form>



                        </div>


                    </td>


                </tr>



            @empty



                <tr>


                    <td colspan="6"
                        class="px-6 py-8 text-center text-gray-500">


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


@endsection