<?php

namespace App\Imports;

use App\DTOs\User\UserDTO;
use App\Services\User\UserService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements ToCollection, WithHeadingRow, WithValidation
{
    private array $errors = [];
    private int $successCount = 0;
    private int $errorCount = 0;
    private int $processedRows = 0;
    private int $currentRowIndex = 0;

    public function __construct(
        private UserService $userService,
    ) {}

    public function collection(Collection $collection)
    {
        $this->processedRows = $collection->count();
        $this->currentRowIndex = 0;

        foreach ($collection as $index => $row) {
            $this->currentRowIndex = $index + 1; // Excel rows start at 1 (plus header row)
            $this->processRow($row);
        }
    }

    public function rules(): array
    {
        return [];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    private function processRow($row): void
    {
        try {
            $validatedData = $this->validateRow($row);

            if ($validatedData['is_valid']) {
                $this->createUser($validatedData['data']);
                $this->successCount++;
            } else {
                $this->errors[] = [
                    'row' => $this->currentRowIndex,
                    'errors' => $validatedData['errors'],
                ];
                $this->errorCount++;
            }
        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $this->currentRowIndex,
                'errors' => ['general' => $e->getMessage()],
            ];
            $this->errorCount++;
        }
    }

    private function validateRow($row): array
    {
        $data = [
            'name' => $row['name'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'password' => $row['password'] ?? config('auth.default_password'),
            'is_active' => $this->parseStatus($row['status'] ?? 'Active'),
            'roles' => $this->parseRoles($row['roles'] ?? null),
        ];

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'is_active' => 'required|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        if ($validator->fails()) {
            return [
                'is_valid' => false,
                'errors' => $validator->errors()->toArray(),
            ];
        }

        return [
            'is_valid' => true,
            'data' => $data,
        ];
    }

    private function createUser(array $data): void
    {
        $userDTO = UserDTO::fromArray($data);
        $user = $this->userService->insertUserData($userDTO);

        if (! empty($data['roles'])) {
            $this->userService->assignUserRole($user, $data['roles']);
        }
    }

    private function parseStatus(?string $status): bool
    {
        if (! $status) {
            return true;
        }

        return strtolower(trim($status)) === 'active';
    }

    private function parseRoles(?string $roles): array
    {
        if (! $roles) {
            return [];
        }

        return array_map('trim', explode(',', $roles));
    }
}
