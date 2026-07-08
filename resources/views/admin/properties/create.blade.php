@extends('layouts.admin')

@section('title','Create Property')

@section('page-title','Create Property')


@section('content')

<div class="max-w-4xl mx-auto">


<div class="bg-white rounded-xl shadow">


<div class="border-b px-6 py-4">

<h2 class="text-2xl font-bold">
Create Property
</h2>

<p class="text-gray-500">
Add a new property and assign an owner.
</p>

</div>



<form method="POST"
action="{{ route('admin.properties.store') }}">

@csrf


<div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">


<div>

<label class="block font-medium mb-2">
Property Name
</label>

<input
type="text"
name="name"
value="{{ old('name') }}"
class="w-full rounded-lg border-gray-300">


@error('name')
<p class="text-red-500 text-sm">
{{ $message }}
</p>
@enderror


</div>



<div>

<label class="block font-medium mb-2">
Owner
</label>


<select
name="landlord_id"
class="w-full rounded-lg border-gray-300">


<option value="">
Select landlord
</option>


@foreach($landlords as $landlord)

<option value="{{ $landlord->id }}">

{{ $landlord->name }}

</option>

@endforeach


</select>


@error('landlord_id')
<p class="text-red-500 text-sm">
{{ $message }}
</p>
@enderror


</div>




<div class="md:col-span-2">

<label class="block font-medium mb-2">
Address
</label>


<input
type="text"
name="address"
value="{{ old('address') }}"
class="w-full rounded-lg border-gray-300">


</div>




<div class="md:col-span-2">

<label class="block font-medium mb-2">
Description
</label>


<textarea
name="description"
rows="4"
class="w-full rounded-lg border-gray-300">{{ old('description') }}</textarea>


</div>



</div>




<div class="border-t px-6 py-4 flex justify-end gap-3">


<a href="{{ route('admin.properties.index') }}"
class="px-5 py-2 border rounded-lg">

Cancel

</a>



<button
class="bg-indigo-600 text-white px-6 py-2 rounded-lg">

Save Property

</button>


</div>


</form>


</div>


</div>


@endsection