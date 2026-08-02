<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tenants Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ca0251;
        }
        .header img {
            max-height: 60px;
            width: auto;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 22px;
            margin: 5px 0 0;
            color: #ca0251;
        }
        .header p {
            color: #6B7280;
            margin: 5px 0 0;
            font-size: 11px;
        }
        .filters {
            background: #F8FAFC;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 11px;
            border: 1px solid #E5E7EB;
        }
        .filters strong {
            color: #111827;
        }
        .badge {
            background: #ca0251;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #ca0251;
            color: #ffffff;
            font-weight: 600;
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #a80244;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 8px 12px;
            border: 1px solid #E5E7EB;
            color: #374151;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #FAFBFC;
        }
        .footer {
            text-align: center;
            color: #6B7280;
            font-size: 10px;
            margin-top: 20px;
            border-top: 1px solid #E5E7EB;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            background-color: #F3F4F6;
            color: #374151;
            font-size: 10px;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 5px;
        }
        .logo-container img {
            max-height: 50px;
            width: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Logo -->
        <div class="logo-container">
            <img src="{{ public_path('images/alendi_logo.jpg') }}" alt="Alendi Logo">
        </div>
        <h1>Tenant List</h1>
        <p>Alendi: For Landlords. For Tenants.</p>
        <p>Generated on {{ $generatedAt->format('d M Y, H:i') }}</p>
        @if(isset($landlord))
            <p>Landlord: {{ e($landlord->name) }}</p>
        @endif
        @if(isset($property))
            <p>Property: {{ e($property->name) }}</p>
        @endif
    </div>

    @if(isset($month) || (isset($paymentStatus) && $paymentStatus != 'all'))
        <div class="filters">
            <strong>Filters Applied:</strong>
            @if(isset($month) && $month)
                <span class="badge">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</span>
            @endif
            @if(isset($paymentStatus) && $paymentStatus != 'all')
                <span class="badge">{{ ucfirst($paymentStatus) }}</span>
            @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tenant Code</th>
                <th>Tenant Name</th>
                <th>Phone</th>
                <th>Property</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $index => $tenant)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ e($tenant->tenant_code) }}</td>
                    <td>{{ e($tenant->name) }}</td>
                    <td>{{ e($tenant->phone) }}</td>
                    <td>{{ e($tenant->property->name ?? $property->name ?? 'N/A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #6B7280; padding: 20px;">
                        No tenants found matching the filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total Tenants: {{ $tenants->count() }}
    </div>
</body>
</html>