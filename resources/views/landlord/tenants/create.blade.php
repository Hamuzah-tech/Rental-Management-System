@extends('layouts.landlord')

@section('title','Add Tenant')

@section('page-title','Add Tenant')


@section('content')


<div class="bg-white rounded-xl shadow p-6">


<form method="POST"
action="{{ route('landlord.tenants.store') }}">


@csrf



<div class="space-y-5">



<div>

<label class="block mb-1">
Full Name
</label>

<input
name="name"
class="w-full border rounded-lg p-3"
required>

</div>




<div>

<label class="block mb-1">
Phone Number
</label>


<input
name="phone"
class="w-full border rounded-lg p-3"
required>

</div>





<div>

<label class="block mb-1">
Email
</label>


<input
name="email"
class="w-full border rounded-lg p-3">

</div>





<div>

<label class="block mb-1">
Property
</label>


<select
name="property_id"
class="w-full border rounded-lg p-3">


@foreach($properties as $property)

<option value="{{ $property->id }}">

{{ $property->name }}

</option>

@endforeach


</select>


</div>





<div>

<label class="block mb-1">
Monthly Rent
</label>


<input
type="number"
name="monthly_rent"
class="w-full border rounded-lg p-3"
required>


</div>





<div>

<label class="block mb-1">
Move In Date
</label>


<input
type="date"
name="move_in_date"
class="w-full border rounded-lg p-3"
required>


</div>





<button
class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

Save Tenant

</button>



</div>


</form>


</div>


@endsection