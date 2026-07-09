@extends('layouts.admin')

@section('title','Landlord Profile')

@section('page-title','Landlord Profile')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">


    <div class="bg-white border border-slate-200 rounded-xl">


        <!-- Header -->
        <div class="border-b border-slate-200 px-6 py-4 flex justify-between items-center">


            <div>

                <h2 class="text-xl font-bold text-slate-800">
                    {{ $landlord->name }}
                </h2>


                <p class="text-sm text-slate-500">
                    Landlord Account Details
                </p>


            </div>



            <a href="{{ route('admin.landlords.edit',$landlord) }}"
               class="flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-sm transition">


                <x-heroicon-o-pencil-square class="w-4 h-4"/>

                Edit


            </a>


        </div>




        <!-- Details -->
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">



            <div>

                <label class="text-sm text-slate-500">
                    Full Name
                </label>

                <p class="font-semibold text-slate-800 mt-1">
                    {{ $landlord->name }}
                </p>

            </div>




            <div>

                <label class="text-sm text-slate-500">
                    Username
                </label>

                <p class="font-semibold text-slate-800 mt-1">
                    {{ $landlord->username }}
                </p>

            </div>





            <div>

                <label class="text-sm text-slate-500">
                    Email
                </label>

                <p class="font-semibold text-slate-800 mt-1">
                    {{ $landlord->email }}
                </p>

            </div>





            <div>

                <label class="text-sm text-slate-500">
                    Phone
                </label>

                <p class="font-semibold text-slate-800 mt-1">
                    {{ $landlord->phone }}
                </p>

            </div>





            <div>

                <label class="text-sm text-slate-500">
                    Second Phone
                </label>

                <p class="font-semibold text-slate-800 mt-1">
                    {{ $landlord->second_phone ?? 'N/A' }}
                </p>

            </div>





            <div>

                <label class="text-sm text-slate-500">
                    Status
                </label>


                @if($landlord->status)

                    <p class="mt-1 inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-sm font-medium">

                        Active

                    </p>


                @else


                    <p class="mt-1 inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-sm font-medium">

                        Suspended

                    </p>


                @endif


            </div>




        </div>


    </div>


</div>


@endsection