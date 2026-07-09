@extends('layouts.admin')

@section('title','Property Details')

@section('page-title','Property Details')


@section('content')


<div class="space-y-6">



    <!-- Property Info -->

    <div class="bg-white border border-slate-200 rounded-xl p-6">


        <div class="flex justify-between items-start">


            <div>


                <h2 class="text-2xl font-bold text-slate-800">

                    {{ $property->name }}

                </h2>


                <p class="text-sm text-slate-500 mt-1">

                    {{ $property->address }}

                </p>


            </div>




            @if($property->status)

                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-sm">

                    Active

                </span>

            @else

                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-sm">

                    Inactive

                </span>

            @endif



        </div>





        <div class="border-t border-slate-200 my-6"></div>





        <div class="grid md:grid-cols-2 gap-6">



            <div class="flex items-center gap-3">


                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">

                    <x-heroicon-o-user class="w-5 h-5 text-slate-300"/>

                </div>


                <div>

                    <p class="text-sm text-slate-500">
                        Owner
                    </p>


                    <p class="font-semibold text-slate-800">

                        {{ $property->landlord->name }}

                    </p>

                </div>


            </div>







            <div class="flex items-center gap-3">


                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">

                    <x-heroicon-o-users class="w-5 h-5 text-slate-300"/>

                </div>


                <div>

                    <p class="text-sm text-slate-500">
                        Total Tenants
                    </p>


                    <p class="font-semibold text-slate-800">

                        {{ $property->tenants->count() }}

                    </p>

                </div>


            </div>




        </div>



    </div>







    <!-- Tenants -->

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">



        <div class="px-6 py-4 border-b border-slate-200">


            <h3 class="text-lg font-bold text-slate-800">

                Tenants

            </h3>


        </div>





        <table class="w-full text-sm">



            <thead class="bg-slate-50">


                <tr class="text-slate-500">


                    <th class="px-5 py-3 text-left">
                        #
                    </th>


                    <th class="px-5 py-3 text-left">
                        Name
                    </th>


                    <th class="px-5 py-3 text-left">
                        Phone
                    </th>


                    <th class="px-5 py-3 text-left">
                        Status
                    </th>


                </tr>


            </thead>





            <tbody>


            @forelse($property->tenants as $index => $tenant)



                <tr class="border-t border-slate-100 hover:bg-slate-50">


                    <td class="px-5 py-3 text-slate-400">

                        {{ $index + 1 }}

                    </td>



                    <td class="px-5 py-3 font-medium text-slate-700">

                        {{ $tenant->name }}

                    </td>



                    <td class="px-5 py-3 text-slate-600">

                        {{ $tenant->phone }}

                    </td>




                    <td class="px-5 py-3">


                        <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">

                            {{ $tenant->status }}

                        </span>


                    </td>



                </tr>



            @empty



                <tr>


                    <td colspan="4"
                        class="text-center py-8 text-slate-500">


                        No tenants yet.


                    </td>


                </tr>



            @endforelse



            </tbody>



        </table>



    </div>





</div>


@endsection