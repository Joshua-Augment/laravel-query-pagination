<?php

namespace AugmentMy\LaravelQueryPagination;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginatedResponse
{
    public static function fromPaginator(LengthAwarePaginator $paginator): JsonResponse
    {
        $lastPage = $paginator->lastPage();

        return response()->json([
            'data' => $paginator->items(),

            'meta' => [
                // Core pagination numbers
                'current_page'   => $paginator->currentPage(),
                'from'           => $paginator->firstItem(),
                'to'             => $paginator->lastItem(),
                'per_page'       => $paginator->perPage(),
                'last_page'      => $lastPage,
                'total'          => $paginator->total(),

                // Path + URLs derived from the paginator
                'path'           => $paginator->path(),
                'first_page_url' => $paginator->url(1),
                'last_page_url'  => $paginator->url($lastPage),
                'next_page_url'  => $paginator->nextPageUrl(),
                'prev_page_url'  => $paginator->previousPageUrl(),

                // Useful booleans
                'has_more_pages' => $paginator->hasMorePages(),
                'on_first_page'  => $paginator->onFirstPage(),
            ],
        ]);
    }
}
