@extends('layouts.landlord')

@section('title','Edit Property')

@section('page-title','Edit Property')

@section('content')

<div class="max-w-3xl mx-auto">
    
<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">



    <!-- Header -->


    <div class="px-6 py-4 border-b border-slate-200">


        <div class="flex items-center gap-3">


            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">


                <x-heroicon-o-pencil-square class="w-5 h-5 text-slate-400"/>


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


    </div>







    <form method="POST"
          action="{{ route('landlord.properties.update',$property) }}">


        @csrf

        @method('PUT')






        <div class="p-6 space-y-5">







            <div>


                <label class="block text-sm font-medium text-slate-700 mb-2">

                    Property Name

                </label>




                <input
                    name="name"
                    value="{{ old('name',$property->name) }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400"
                    required>



            </div>








            <div>


                <label class="block text-sm font-medium text-slate-700 mb-2">

                    Address

                </label>





                <input
                    name="address"
                    value="{{ old('address',$property->address) }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">





            </div>









            <div>


                <label class="block text-sm font-medium text-slate-700 mb-2">

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





            <a href="{{ route('landlord.properties.index') }}"
               class="px-5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">


                Cancel


            </a>







            <button
                class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-xl transition flex items-center gap-2">





                <x-heroicon-o-check class="w-4 h-4"/>



                Update Property





            </button>





        </div>






    </form>





</div>
</div>

@endsection
