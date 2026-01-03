<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

/**
 * Helper for composing Eloquent query filters and text search.
 *
 * Provides utilities to:
 * - Apply "LIKE" searches across multiple model fields
 * - Search within relations via whereHas
 * - Combine exact-match select filters with text searches
 * - Run unified searches across model and relations
 *
 * Intended usage:
 * - Controllers and services, repositories, building index/list endpoints
 * - Reusable filtering for searchable tables and APIs
 * - Used in export endpoints to filter large datasets
 *
 * Guarantees:
 * - Works with any Illuminate\Database\Eloquent\Builder
 * - Adds conditions in nested closures to preserve existing query constraints
 *
 * See also: applyFilters, applyUnifiedSearch, applyRelationSearchFilters,
 * applyRelationSelectFilters, applyAccurateRelationSearchFilters.
 */
class EloquentFilterHelper
{
    /**
     * Apply search filters to a model.
     *
     * @param  string|null $searchText   The text to search for.
     * @param  Builder     $model        The model to search on.
     * @param  string[]    $searchFields The fields to search in.
     * @return Builder     The filtered model.
     */
    public static function applySearchFilters(
        ?string $searchText,
        Builder $model,
        array $searchFields
    ): Builder {
        if (empty($searchText)) {
            return $model;
        }

        return $model->where(function (Builder $query) use ($searchText, $searchFields) {
            $query->where(function (Builder $query) use ($searchText, $searchFields) {
                foreach ($searchFields as $field) {
                    $query->orWhere($field, 'LIKE', "%{$searchText}%");
                }
            });
        });
    }

    /**
     * Apply Relation Search Filters to a model.
     *
     * @param  string   $relation     The relation to search within.
     * @param  string   $searchText   The text to search for within the relation.
     * @param  string[] $searchFields The fields to search in within the relation.
     * @param  Builder  $model        The model to search on.
     * @return Builder  The filtered model.
     */
    public static function applyRelationSearchFilters(
        string $relation,
        ?string $searchText,
        array $searchFields,
        Builder $model
    ): Builder {
        if (empty($searchText)) {
            return $model;
        }

        return $model->whereHas($relation, function ($query) use ($searchText, $searchFields) {
            $query->where(function ($query) use ($searchText, $searchFields) {
                foreach ($searchFields as $field) {
                    $query->orWhere($field, 'LIKE', "%{$searchText}%");
                }
            });
        });
    }

    /**
     * Apply Select filters to a model.
     *
     * @param  Builder              $model        The model to search on.
     * @param  array<string, mixed> $selectFields The fields to search in.
     * @return Builder              The filtered model.
     */
    public static function applySelectFilters(
        Builder $model,
        array $selectFields
    ): Builder {
        $selectFields = array_filter($selectFields, function ($value) {
            return ! is_null($value) && $value !== '';
        });

        return $model->where(function (Builder $query) use ($selectFields) {
            foreach ($selectFields as $field => $value) {
                $query->where($field, '=', $value);
            }
        });
    }

    /**
     * Apply Select filters to a model relation.
     *
     * @param  string               $relation     The relation to filter within.
     * @param  Builder              $model        The model to search on.
     * @param  array<string, mixed> $selectFields The fields to search in.
     * @return Builder              The filtered model.
     */
    public static function applyRelationSelectFilters(
        string $relation,
        Builder $model,
        array $selectFields
    ): Builder {
        $selectFields = array_filter($selectFields, function ($value) {
            return ! is_null($value) && $value !== '';
        });

        return $model->whereHas($relation, function (Builder $query) use ($selectFields) {
            $query->where(function ($query) use ($selectFields) {
                foreach ($selectFields as $field => $value) {
                    $query->orWhere($field, '=', $value);
                }
            });
        });
    }

    /**
     * Apply Filters to a model.
     *
     * @param  ?string              $searchText   The text to search for.
     * @param  array<string>        $searchFields The fields to search in.
     * @param  array<string, mixed> $selectFields The fields to select.
     * @param  Builder              $model        The model to search on.
     * @param  ?string              $relation     The model relationship to search on.
     * @return Builder              The filtered model.
     */
    public static function applyFilters(
        ?string $searchText,
        array $searchFields,
        array $selectFields,
        Builder $model,
    ): Builder {
        if ($searchText && ! empty($searchFields)) {
            $model = self::applySearchFilters($searchText, $model, $searchFields);
        }

        if (! empty($selectFields)) {
            $model = self::applySelectFilters($model, $selectFields);
        }

        return $model;
    }

    /**
     * Apply unified "LIKE" search across model fields and relations.
     *
     * - Applies `orWhere` LIKE conditions to the provided `$modelSearchFields`
     *   on the root model.
     * - Applies `orWhereHas` to each relation in `$relationSearchConfig`,
     *   using either the relation's specific field list or falling back to
     *   `$modelSearchFields` when `null`.
     *
     * @param  Builder     $query                Query builder to augment.
     * @param  string|null $searchText           Search text (trimmed; empty string short-circuits).
     * @param  string[]    $modelSearchFields    Fields to search on the root model.
     * @param  array       $relationSearchConfig Map of relation => fields|null.
     *                                           Example: ['profile' => ['bio'], 'company' => null]
     * @return Builder     The augmented query builder.
     *
     * @example
     * // Only model fields
     * $query = User::query();
     * $query = app(FilterHelper::class)->applyUnifiedSearch($query, 'john', ['name', 'email']);
     * @example
     * // Model + relations, with per-relation fields and fallback
     * $query = User::query()->with(['profile', 'company']);
     * $query = app(FilterHelper::class)->applyUnifiedSearch(
     *     $query,
     *     'john',
     *     ['name', 'email'],
     *     [
     *         'profile' => ['bio', 'address'],
     *         'company' => null, // fallback to ['name', 'email']
     *     ],
     * );
     */
    public static function applyUnifiedSearch(
        Builder $query,
        ?string $searchText,
        array $modelSearchFields,
        array $relationSearchConfig = []
    ): Builder {
        if (empty($searchText) || empty($modelSearchFields)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($searchText, $modelSearchFields, $relationSearchConfig) {
            // Search in model fields
            $this->applySearchConditions($q, $searchText, $modelSearchFields);

            // Search in relations if configured
            foreach ($relationSearchConfig as $relationName => $relationFields) {
                $fieldsToSearch = $relationFields ?? $modelSearchFields;

                $q->orWhereHas($relationName, function (Builder $relationQuery) use ($searchText, $fieldsToSearch) {
                    $this->applySearchConditions($relationQuery, $searchText, $fieldsToSearch);
                });
            }
        });
    }

    /**
     * Apply Accurate Relation Search Filters to a model (Optimized for Name Search)
     *
     * @param  string   $relation     The relation to search within.
     * @param  string   $searchText   The text to search for within the relation.
     * @param  string[] $searchFields The fields to search in within the relation.
     * @param  Builder  $model        The model to search on.
     * @return Builder  The filtered model.
     */
    public static function applyAccurateRelationSearchFilters(
        string $relation,
        ?string $searchText,
        array $searchFields,
        Builder $model
    ): Builder {
        if (empty($searchText)) {
            return $model;
        }

        return $model->whereHas($relation, function ($query) use ($searchText, $searchFields) {
            $query->where(function ($query) use ($searchText, $searchFields) {
                foreach ($searchFields as $field) {
                    $query->orWhere($field, 'LIKE', "{$searchText}%");
                }
            });
        });
    }

    /**
     * Apply search conditions to a query builder.
     *
     * @param Builder  $query      The query builder instance.
     * @param string   $searchText The text to search for.
     * @param string[] $fields     The fields to search in.
     */
    private function applySearchConditions(Builder $query, string $searchText, array $fields): void
    {
        $query->where(function (Builder $q) use ($searchText, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$searchText}%");
            }
        });
    }
}
