@extends('layouts.landlord')

@section('title','Properties')

@section('page-title','My Properties')


@section('content')


<div class="space-y-6">


<div class="bg-white p-6 rounded-xl shadow flex justify-between">


<h2 class="text-2xl font-bold">
Properties
</h2>


<a href="{{ route('landlord.properties.create') }}"
class="bg-indigo-600 text-white px-5 py-2 rounded-lg">

+ Add Property

</a>


</div>




@if(session('success'))

<div class="bg-green-100 text-green-700 p-4 rounded-lg">

{{ session('success') }}

</div>

@endif




<div class="bg-white rounded-xl shadow overflow-hidden">


<table class="w-full">


<thead class="bg-gray-100">

<tr>

<th class="p-4 text-left">
Name
</th>


<th class="p-4 text-left">
Address
</th>


<th class="p-4 text-left">
Status
</th>


<th class="p-4">
Actions
</th>

</tr>

</thead>



<tbody>


@foreach($properties as $property)


<tr class="border-t">


<td class="p-4">
{{ $property->name }}
</td>


<td class="p-4">
{{ $property->address }}
</td>



<td class="p-4">


@if($property->status)

<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full">
Active
</span>

@else

<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full">
Inactive
</span>

@endif


</td>



<td class="p-4">


<a href="{{ route('landlord.properties.edit',$property) }}"
class="text-indigo-600">

Edit

</a>



<form method="POST"
action="{{ route('landlord.properties.destroy',$property) }}"
class="inline">

@csrf
@method('DELETE')


<button class="text-red-600 ml-3">

Delete

</button>


</form>



</td>


</tr>


@endforeach


</tbody>


</table>


</div>



{{ $properties->links() }}


</div>


@endsection