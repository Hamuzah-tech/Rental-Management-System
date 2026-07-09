@extends('layouts.landlord')

@section('title','Add Property')

@section('page-title','Add Property')


@section('content')


<div class="max-w-3xl mx-auto">


    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">


        <!-- Header -->

        <div class="px-6 py-4 border-b border-slate-200 flex items-center gap-3">


            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">

                <x-heroicon-o-building-office class="w-6 h-6 text-slate-400"/>

            </div>


            <div>

                <h2 class="text-lg font-semibold text-slate-800">

                    Add Property

                </h2>


                <p class="text-sm text-slate-500">

                    Create a new property in your portfolio.

                </p>

            </div>


        </div>





        <form method="POST"
              action="{{ route('landlord.properties.store') }}">


            @csrf



            <div class="p-6 space-y-5">





                <!-- Property Name -->

                <div>


                    <label class="block text-sm font-medium text-slate-700 mb-2">

                        Property Name

                    </label>



                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm"
                        placeholder="Example: Sunrise Hostel"
                        required>


                </div>







                <!-- Address -->

                <div>


                    <label class="block text-sm font-medium text-slate-700 mb-2">

                        Address

                        <span class="text-slate-400 font-normal">
                            (Optional)
                        </span>

                    </label>



                    <input
                        type="text"
                        name="address"
                        value="{{ old('address') }}"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm"
                        placeholder="Example: Blantyre">


                </div>







                <!-- Description -->

                <div>


                    <label class="block text-sm font-medium text-slate-700 mb-2">

                        Description

                        <span class="text-slate-400 font-normal">
                            (Optional)
                        </span>

                    </label>



                    <textarea
                        name="description"
                        rows="4"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm"
                        placeholder="Property description">{{ old('description') }}</textarea>


                </div>




            </div>







            <!-- Footer -->

            <div class="border-t border-slate-200 px-6 py-4 flex justify-end gap-3">



                <a href="{{ route('landlord.properties.index') }}"
                   class="px-5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm transition">


                    Cancel


                </a>





                <button
                    class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-xl text-sm transition flex items-center gap-2">


                    <x-heroicon-o-check class="w-4 h-4"/>


                    Save Property


                </button>



            </div>




        </form>



    </div>


</div>


@endsection