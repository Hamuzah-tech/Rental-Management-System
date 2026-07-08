@extends('layouts.admin')

@section('title','Edit Landlord')

@section('page-title','Edit Landlord')

@section('content')


<div class="max-w-4xl mx-auto">


<div class="bg-white rounded-xl shadow">


<div class="border-b px-6 py-4">

<h2 class="text-2xl font-bold">
Edit Landlord
</h2>

</div>



<form method="POST"
      action="{{ route('admin.landlords.update',$landlord) }}">


@csrf
@method('PUT')


<div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">


<div>

<label class="block mb-2 font-medium">
Name
</label>

<input
name="name"
value="{{ old('name',$landlord->name) }}"
class="w-full rounded-lg border-gray-300">


</div>



<div>

<label class="block mb-2 font-medium">
Username
</label>

<input
name="username"
value="{{ old('username',$landlord->username) }}"
class="w-full rounded-lg border-gray-300">


</div>



<div>

<label class="block mb-2 font-medium">
Email
</label>

<input
type="email"
name="email"
value="{{ old('email',$landlord->email) }}"
class="w-full rounded-lg border-gray-300">


</div>



<div>

<label class="block mb-2 font-medium">
Phone
</label>

<input
name="phone"
value="{{ old('phone',$landlord->phone) }}"
class="w-full rounded-lg border-gray-300">


</div>



<div>

<label class="block mb-2 font-medium">
Second Phone
</label>

<input
name="second_phone"
value="{{ old('second_phone',$landlord->second_phone) }}"
class="w-full rounded-lg border-gray-300">


</div>


</div>



<div class="border-t px-6 py-4 flex justify-end gap-3">


<a href="{{ route('admin.landlords.index') }}"
class="px-5 py-2 border rounded-lg">

Cancel

</a>


<button
class="px-6 py-2 bg-indigo-600 text-white rounded-lg">

Update

</button>


</div>


</form>


</div>


</div>


@endsection