@extends('layouts.admin')

@section('title','Landlord Profile')

@section('page-title','Landlord Profile')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">


    <div class="bg-white rounded-xl shadow">


        <div class="border-b px-6 py-4 flex justify-between items-center">


            <div>

                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $landlord->name }}
                </h2>

                <p class="text-gray-500">
                    Landlord Account Details
                </p>

            </div>


            <a href="{{ route('admin.landlords.edit',$landlord) }}"
               class="bg-indigo-600 text-white px-5 py-2 rounded-lg">

                Edit

            </a>


        </div>



        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">


            <div>
                <label class="text-gray-500">
                    Full Name
                </label>

                <p class="font-semibold">
                    {{ $landlord->name }}
                </p>
            </div>



            <div>
                <label class="text-gray-500">
                    Username
                </label>

                <p class="font-semibold">
                    {{ $landlord->username }}
                </p>
            </div>



            <div>
                <label class="text-gray-500">
                    Email
                </label>

                <p class="font-semibold">
                    {{ $landlord->email }}
                </p>
            </div>



            <div>
                <label class="text-gray-500">
                    Phone
                </label>

                <p class="font-semibold">
                    {{ $landlord->phone }}
                </p>
            </div>



            <div>
                <label class="text-gray-500">
                    Second Phone
                </label>

                <p class="font-semibold">
                    {{ $landlord->second_phone ?? 'N/A' }}
                </p>
            </div>



            <div>
                <label class="text-gray-500">
                    Status
                </label>


                @if($landlord->status)

                    <p class="text-green-600 font-bold">
                        Active
                    </p>

                @else

                    <p class="text-red-600 font-bold">
                        Suspended
                    </p>

                @endif

            </div>


        </div>


    </div>



</div>


@endsection