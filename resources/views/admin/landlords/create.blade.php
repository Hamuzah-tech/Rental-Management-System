@if(session('success'))

    <div style="padding:20px;background:#d4edda;border:1px solid #28a745;margin-bottom:20px;">

        <h3>✅ Landlord Created Successfully</h3>

        <p><strong>Email:</strong> {{ session('success')['email'] }}</p>

        <p><strong>Temporary Password:</strong> {{ session('success')['password'] }}</p>

    </div>

@endif

<h1>Create Landlord</h1>

<form method="POST" action="/admin/landlords/store">
    @csrf

    <input type="text" name="name" placeholder="Full Name" required>
    <br><br>

    <input type="email" name="email" placeholder="Email" required>
    <br><br>

    <input type="text" name="phone" placeholder="Phone (optional)">
    <br><br>

    <button type="submit">Create Landlord</button>
</form>