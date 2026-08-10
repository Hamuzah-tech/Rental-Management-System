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
            margin: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #ca0251;
        }
        .header img {
            max-height: 60px;
            width: auto;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 24px;
            margin: 5px 0 0;
            color: #ca0251;
            font-weight: 700;
        }
        .header .subtitle {
            color: #6B7280;
            margin: 5px 0 0;
            font-size: 12px;
        }
        .header .meta-info {
            color: #6B7280;
            margin: 3px 0 0;
            font-size: 10px;
        }
        .filters {
            background: #F8FAFC;
            padding: 12px 15px;
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
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 10px;
            display: inline-block;
            margin: 2px 4px 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
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
        .logo-container {
            text-align: center;
            margin-bottom: 5px;
        }
        .logo-container img {
            max-height: 50px;
            width: auto;
        }
        .text-muted {
            color: #6B7280;
        }
        .text-center {
            text-align: center;
        }
        .summary-box {
            background: #F8FAFC;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #E5E7EB;
            display: inline-block;
        }
        .summary-box span {
            margin-right: 20px;
        }
        .summary-box strong {
            color: #111827;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Logo -->
        <div class="logo-container">
            <img src="{{ public_path('images/alendi_logo.jpg') }}" alt="Alendi Logo">
        </div>
        <h1>Tenant List Report</h1>
        <p class="subtitle">Alendi: For Landlords. For Tenants.</p>
        <p class="meta-info">Generated on {{ $generatedAt->format('d M Y, H:i') }}</p>
        @if(isset($landlord))
            <p class="meta-info">Landlord: {{ e($landlord->name) }}</p>
        @endif
        @if(isset($property))
            <p class="meta-info">Property: {{ e($property->name) }}</p>
        @endif
    </div>

    @if(isset($month) && $month || (isset($paymentStatus) && $paymentStatus != 'all') || isset($search))
        <div class="filters">
            <strong>Filters Applied:</strong>
            @if(isset($month) && $month)
                <span class="badge">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</span>
            @endif
            @if(isset($paymentStatus) && $paymentStatus != 'all')
                <span class="badge">{{ ucfirst($paymentStatus) }}</span>
            @endif
            @if(isset($search) && $search)
                <span class="badge">Search: {{ e($search) }}</span>
            @endif
        </div>
    @endif

    <div class="summary-box">
        <span><strong>Total Tenants:</strong> {{ $tenants->count() }}</span>
        @if(isset($property))
            <span><strong>Property:</strong> {{ e($property->name) }}</span>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="35%">Tenant Name</th>
                <th width="25%">Phone</th>
                <th width="35%">Email</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $index => $tenant)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><strong>{{ e($tenant->name) }}</strong></td>
                    <td>{{ e($tenant->phone) }}</td>
                    <td>{{ e($tenant->email) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted" style="padding: 20px;">
                        No tenants found matching the filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by Alendi Property Management System</p>
    </div>
</body>
</html>