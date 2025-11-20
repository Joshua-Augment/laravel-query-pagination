<?php

namespace AugmentMy\LaravelQueryPagination;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginatedQuery
{
    public function __construct(
        protected Builder $baseQuery,
        protected array $searchable = [],
        protected array $filterable = [],
        protected array $sortable = [],
        protected ?string $defaultSort = null,
        protected string $defaultSortDir = 'asc',
    ) {}


    /**
     * Convenience: build paginator from the Request and return JSON response.
     */
    public function toResponse(Request $request): JsonResponse
    {
        $paginator = $this->fromRequest($request);

        return PaginatedResponse::fromPaginator($paginator);
    }


    public function fromRequest(Request $request): LengthAwarePaginator
    {
        $query = clone $this->baseQuery;

        // Search
        if ($search = $request->query('search')) {
            $query->where(function (Builder $q) use ($search) {
                foreach ($this->searchable as $column) {
                    $q->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        // Filters
        $filters = (array) $request->query('filters', []);
        foreach ($filters as $field => $value) {
            if (!in_array($field, $this->filterable, true)) continue;
            if ($value === null || $value === '') continue;
            $query->where($field, $value);
        }

        // Sorting
        $sortBy = $request->query('sort_by', $this->defaultSort);
        $sortDir = $request->query('sort_dir', $this->defaultSortDir);

        if ($sortBy && in_array($sortBy, $this->sortable, true)) {
            $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
        }

        // Pagination
        $perPage = (int) $request->query('per_page', 15);
        $perPage = min(max($perPage, 1), 100);

        return $query->paginate($perPage)->appends($request->query());
    }

    /**
     * Optional ergonomic static constructor if you like the style.
     */
    public static function make(
        Builder $baseQuery,
        array $searchable = [],
        array $filterable = [],
        array $sortable = [],
        ?string $defaultSort = null,
        string $defaultSortDir = 'asc',
    ): self {
        return new self(
            baseQuery: $baseQuery,
            searchable: $searchable,
            filterable: $filterable,
            sortable: $sortable,
            defaultSort: $defaultSort,
            defaultSortDir: $defaultSortDir,
        );
    }
}
