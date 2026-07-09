@extends('layouts.landlord')

@section('title','Add Tenant')

@section('page-title','Add Tenant')

@section('content')

<div class="max-w-3xl mx-auto">

<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">


    <!-- Header -->


    <div class="px-6 py-4 border-b border-slate-200">


        <div class="flex items-center gap-3">


            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">

                <x-heroicon-o-user-plus class="w-5 h-5 text-slate-400"/>

            </div>


            <div>


                <h2 class="text-xl font-bold text-slate-800">

                    Add Tenant

                </h2>


                <p class="text-sm text-slate-500">

                    Create a new tenant account.

                </p>


            </div>


        </div>


    </div>






    <form method="POST"
          action="{{ route('landlord.tenants.store') }}">


        @csrf




        <div class="p-6 space-y-5">






            <div>


                <label class="block text-sm font-medium text-slate-700 mb-2">

                    Full Name

                </label>



                <input
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                    required>


            </div>







            <div>


                <label class="block text-sm font-medium text-slate-700 mb-2">

                    Phone Number

                </label>



                <input
                    name="phone"
                    value="{{ old('phone') }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                    required>


            </div>







            <div>


                <label class="block text-sm font-medium text-slate-700 mb-2">

                    Email

                </label>



                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">


            </div>







            <div>


                <label class="block text-sm font-medium text-slate-700 mb-2">

                    Property

                </label>



                <select
                    name="property_id"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">



                    @foreach($properties as $property)


                        <option value="{{ $property->id }}">

                            {{ $property->name }}

                        </option>


                    @endforeach



                </select>


            </div>







            <div>


                <label class="block text-sm font-medium text-slate-700 mb-2">

                    Monthly Rent

                </label>



                <input
                    type="number"
                    name="monthly_rent"
                    value="{{ old('monthly_rent') }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                    required>


            </div>







            <div>


                <label class="block text-sm font-medium text-slate-700 mb-2">

                    Move In Date

                </label>



                <input
                    type="date"
                    name="move_in_date"
                    value="{{ old('move_in_date') }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                    required>


            </div>





        </div>







        <!-- Footer -->


        <div class="border-t border-slate-200 px-6 py-4 flex justify-end gap-3">



            <a href="{{ route('landlord.tenants.index') }}"
               class="px-5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">


                Cancel


            </a>





            <button
                class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-xl transition flex items-center gap-2">



                <x-heroicon-o-check class="w-4 h-4"/>


                Save Tenant



            </button>



        </div>




    </form>



</div>

</div>

@endsection
