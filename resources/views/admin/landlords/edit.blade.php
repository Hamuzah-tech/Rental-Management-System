@extends('layouts.admin')

@section('title','Edit Landlord')

@section('page-title','Edit Landlord')

@section('content')

<div class="max-w-4xl mx-auto">


    <div class="bg-white border border-slate-200 rounded-xl">


        <!-- Header -->
        <div class="border-b border-slate-200 px-6 py-4">

            <h2 class="text-xl font-bold text-slate-800">
                Edit Landlord
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Update landlord account information.
            </p>

        </div>



        <form method="POST"
              action="{{ route('admin.landlords.update',$landlord) }}">


            @csrf
            @method('PUT')



            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">



                <div>

                    <label class="block mb-2 text-sm font-medium text-slate-700">
                        Name
                    </label>

                    <input
                        name="name"
                        value="{{ old('name',$landlord->name) }}"
                        class="w-full rounded-xl border border-slate-200 p-3 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none">

                </div>




                <div>

                    <label class="block mb-2 text-sm font-medium text-slate-700">
                        Username
                    </label>

                    <input
                        name="username"
                        value="{{ old('username',$landlord->username) }}"
                        class="w-full rounded-xl border border-slate-200 p-3 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none">

                </div>




                <div>

                    <label class="block mb-2 text-sm font-medium text-slate-700">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email',$landlord->email) }}"
                        class="w-full rounded-xl border border-slate-200 p-3 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none">

                </div>




                <div>

                    <label class="block mb-2 text-sm font-medium text-slate-700">
                        Phone
                    </label>

                    <input
                        name="phone"
                        value="{{ old('phone',$landlord->phone) }}"
                        class="w-full rounded-xl border border-slate-200 p-3 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none">

                </div>




                <div>

                    <label class="block mb-2 text-sm font-medium text-slate-700">
                        Second Phone
                    </label>

                    <input
                        name="second_phone"
                        value="{{ old('second_phone',$landlord->second_phone) }}"
                        class="w-full rounded-xl border border-slate-200 p-3 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none">

                </div>



            </div>




            <!-- Footer -->
            <div class="border-t border-slate-200 px-6 py-4 flex justify-end gap-3">


                <a href="{{ route('admin.landlords.index') }}"
                   class="px-5 py-2 border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 transition">

                    Cancel

                </a>




                <button
                    type="submit"
                    class="flex items-center gap-2 px-6 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl transition">


                    <x-heroicon-o-check class="w-5 h-5"/>

                    Update

                </button>



            </div>


        </form>


    </div>


</div>


@endsection