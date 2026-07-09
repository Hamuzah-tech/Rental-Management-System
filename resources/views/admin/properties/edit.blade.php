@extends('layouts.admin')

@section('title','Edit Property')

@section('page-title','Edit Property')


@section('content')


<div class="max-w-4xl mx-auto">



    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">



        <div class="px-6 py-4 border-b border-slate-200 flex items-center gap-3">


            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">

                <x-heroicon-o-building-office class="w-5 h-5 text-slate-400"/>

            </div>


            <div>

                <h2 class="text-xl font-bold text-slate-800">
                    Edit Property
                </h2>

                <p class="text-sm text-slate-500">
                    Update property information.
                </p>

            </div>


        </div>







        <form method="POST"
              action="{{ route('admin.properties.update',$property) }}">


            @csrf

            @method('PUT')



            <div class="p-6 space-y-5">





                <!-- Property Name -->


                <div>


                    <label class="block mb-2 text-sm font-medium text-slate-700">

                        Property Name

                    </label>



                    <input
                        type="text"
                        name="name"
                        value="{{ old('name',$property->name) }}"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">


                </div>






                <!-- Owner -->


                <div>


                    <label class="block mb-2 text-sm font-medium text-slate-700">

                        Owner

                    </label>



                    <select
                        name="landlord_id"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">



                        @foreach($landlords as $landlord)


                            <option
                                value="{{ $landlord->id }}"
                                @if($property->landlord_id == $landlord->id)
                                    selected
                                @endif
                            >

                                {{ $landlord->name }}

                            </option>


                        @endforeach



                    </select>



                </div>







                <!-- Address -->


                <div>


                    <label class="block mb-2 text-sm font-medium text-slate-700">

                        Address

                    </label>



                    <input
                        type="text"
                        name="address"
                        value="{{ old('address',$property->address) }}"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">


                </div>







                <!-- Description -->


                <div>


                    <label class="block mb-2 text-sm font-medium text-slate-700">

                        Description

                    </label>



                    <textarea
                        name="description"
                        rows="4"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">{{ old('description',$property->description) }}</textarea>



                </div>




            </div>







            <!-- Footer -->


            <div class="border-t border-slate-200 px-6 py-4 flex justify-end gap-3">


                <a href="{{ route('admin.properties.index') }}"
                   class="px-5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">


                    Cancel


                </a>




                <button
                    class="flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-xl transition">



                    <x-heroicon-o-check class="w-4 h-4"/>


                    Update Property



                </button>



            </div>




        </form>



    </div>



</div>



@endsection