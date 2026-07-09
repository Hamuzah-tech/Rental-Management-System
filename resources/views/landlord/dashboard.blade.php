@extends('layouts.landlord')

@section('title','Landlord Dashboard')

@section('page-title','Dashboard')


@section('content')


<div class="space-y-6">



    <!-- Welcome -->

    <div class="bg-white border border-slate-200 rounded-xl p-6">


        <div class="flex items-center gap-3">


            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">

                <x-heroicon-o-chart-bar class="w-6 h-6 text-slate-400"/>

            </div>


            <div>

                <h2 class="text-xl font-bold text-slate-800">

                    Welcome Back

                </h2>


                <p class="text-sm text-slate-500">

                    Overview of your rental management activities.

                </p>


            </div>


        </div>


    </div>







    <!-- Statistics -->


    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">






        <!-- Properties -->


        <div class="bg-white border border-slate-200 rounded-xl p-6">


            <div class="flex items-center justify-between">


                <div>


                    <p class="text-sm text-slate-500">

                        Properties

                    </p>


                    <h3 class="text-3xl font-bold text-slate-800 mt-2">

                        {{ $properties }}

                    </h3>


                </div>



                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center">


                    <x-heroicon-o-building-office
                        class="w-7 h-7 text-slate-400"/>


                </div>


            </div>


        </div>







        <!-- Tenants -->


        <div class="bg-white border border-slate-200 rounded-xl p-6">


            <div class="flex items-center justify-between">


                <div>


                    <p class="text-sm text-slate-500">

                        Tenants

                    </p>


                    <h3 class="text-3xl font-bold text-slate-800 mt-2">

                        {{ $tenants }}

                    </h3>


                </div>



                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center">


                    <x-heroicon-o-user-group
                        class="w-7 h-7 text-slate-400"/>


                </div>


            </div>


        </div>








        <!-- Active Tenants -->


        <div class="bg-white border border-slate-200 rounded-xl p-6">


            <div class="flex items-center justify-between">


                <div>


                    <p class="text-sm text-slate-500">

                        Active Tenants

                    </p>


                    <h3 class="text-3xl font-bold text-slate-800 mt-2">

                        {{ $activeTenants }}

                    </h3>


                </div>



                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center">


                    <x-heroicon-o-check-circle
                        class="w-7 h-7 text-slate-400"/>


                </div>


            </div>


        </div>







    </div>





</div>


@endsection