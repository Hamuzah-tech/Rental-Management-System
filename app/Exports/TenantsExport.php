<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TenantsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $tenants;

    public function __construct($tenants)
    {
        $this->tenants = $tenants;
    }

    public function collection()
    {
        return $this->tenants;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tenant Code',
            'Name',
            'Email',
            'Phone',
            'Property',
            'Monthly Rent (MK)',
            'Status',
            'Created At'
        ];
    }

    public function map($tenant): array
    {
        return [
            $tenant->id,
            $tenant->tenant_code,
            $tenant->name,
            $tenant->email ?? 'N/A',
            $tenant->phone,
            $tenant->property->name ?? 'N/A',
            number_format($tenant->monthly_rent, 2),
            $tenant->status,
            $tenant->created_at->format('Y-m-d H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}