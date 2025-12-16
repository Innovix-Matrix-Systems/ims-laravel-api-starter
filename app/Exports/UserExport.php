<?php

namespace App\Exports;

use App\DTOs\User\UserFilterDTO;
use App\Enums\UserStatus;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class UserExport implements FromCollection, WithEvents, WithHeadings, WithMapping
{
    private int $totalUsers = 0;

    public function __construct(
        protected UserFilterDTO $filters
    ) {}

    public function collection(): Collection
    {
        $this->totalUsers = User::count();

        return app(UserRepositoryInterface::class)->getAllWithFilters($this->filters);
    }

    /** @param User $user */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name ?? 'N/A',
            $user->email ?? 'N/A',
            $user->phone ?? 'N/A',
            $user->is_active == UserStatus::ACTIVE->value ? 'Active' : 'Inactive',
            $user->roles->pluck('name')->implode(', ') ?: 'N/A',
            $user->created_at?->format('d-m-Y H:i:s') ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Designation',
            'Address',
            'Status',
            'Roles',
            'Branch',
            'Created At',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestRow();

                $totalRow = $lastRow + 2;
                $event->sheet->setCellValue('A' . $totalRow, 'Summary Statistics:');
                $event->sheet->getStyle('A' . $totalRow)->getFont()->setBold(true);

                $statsRow = $totalRow + 1;
                $event->sheet->setCellValue('A' . $statsRow, "Total Users: {$this->totalUsers}");
                $event->sheet->mergeCells('A' . $statsRow . ':J' . $statsRow);

                $noteRow = $statsRow + 1;
                $filters = [];
                if ($this->filters->search) {
                    $filters[] = "Search: {$this->filters->search}";
                }
                if ($this->filters->isActive !== null) {
                    $filters[] = 'Status: ' . ($this->filters->isActive ? 'Active' : 'Inactive');
                }
                if ($this->filters->roleName) {
                    $filters[] = "Role: {$this->filters->roleName}";
                }
                $filterText = ! empty($filters) ? ' (' . implode(', ', $filters) . ')' : '';
                $event->sheet->setCellValue('A' . $noteRow, 'User Export Generated on: ' . now()->format('Y-m-d H:i:s') . $filterText);
                $event->sheet->mergeCells('A' . $noteRow . ':J' . $noteRow);
            },
        ];
    }
}
