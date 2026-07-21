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
            '#',
            'Tenant Code',
            'Tenant Name',
            'Phone',
            'Property'
        ];
    }

    public function map($tenant): array
    {
        return [
            $tenant->id,
            $tenant->tenant_code,
            $tenant->name,
            $tenant->phone,
            $tenant->property->name ?? 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}