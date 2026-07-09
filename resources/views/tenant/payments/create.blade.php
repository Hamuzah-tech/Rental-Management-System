<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Record Payment</title>

@vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="bg-gray-100">

<div class="max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow p-8">

<h1 class="text-3xl font-bold text-indigo-700 mb-6">

Record Payment

</h1>

<form method="POST"
action="{{ route('tenant.payments.store') }}"
enctype="multipart/form-data">

@csrf

<div class="space-y-5">

<div>

<label class="block mb-1">

Tenant Code

</label>

<input
name="tenant_code"
required
class="w-full border rounded-lg p-3">

</div>

<div>

<label class="block mb-1">

Tenant Name

</label>

<input
name="tenant_name"
required
class="w-full border rounded-lg p-3">

</div>

<div>

<label class="block mb-1">

Payment Month

</label>

<input
type="month"
name="payment_month"
required
class="w-full border rounded-lg p-3">

</div>

<div>

<label class="block mb-1">

Amount Paid

</label>

<input
type="number"
step="0.01"
name="amount"
required
class="w-full border rounded-lg p-3">

</div>

<div>

<label class="block mb-1">

Payment Screenshot

</label>

<input
type="file"
name="screenshot"
required
class="w-full border rounded-lg p-3">

</div>

<button
class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg">

Submit Payment

</button>

</div>

</form>

</div>

</body>
</html>