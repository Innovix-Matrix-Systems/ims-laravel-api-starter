<?php

namespace App\Http\Mappers;

use Illuminate\Support\Collection;

/**
 * @template TDTO of object
 */
abstract class AbstractBulkMapper
{
    /**
     * Convert collection of DTOs to array for bulk insert
     *
     * @param  Collection<TDTO> $dtos  Collection of DTOs
     * @param  array            $extra Additional data needed for mapping
     * @return array            Array ready for bulk insert
     */
    abstract public static function toBulkInsertArray(Collection $dtos, array $extra = []): array;

    /** Get base fields for bulk insert including audit fields */
    protected static function getBaseFields(): array
    {
        return array_merge(
            self::getTimestamps(),
            self::getAuditFields()
        );
    }

    /** Get timestamp fields for bulk insert */
    protected static function getTimestamps(): array
    {
        return [
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /** Get audit fields for bulk insert */
    protected static function getAuditFields(): array
    {
        return [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];
    }

    /**
     * Validate required extra parameters
     *
     * @throws \InvalidArgumentException
     */
    protected static function validateExtra(array $extra, array $required): void
    {
        foreach ($required as $field) {
            if (! isset($extra[$field])) {
                throw new \InvalidArgumentException("$field is required");
            }
        }
    }
}
