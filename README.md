# Laravel Query Pagination

A lightweight package for building **consistent, API-friendly pagination, sorting, filtering, and search** endpoints in Laravel — now using a **single unified class (`PaginatedQuery`)** for both querying and responding.

This package standardizes the query params and JSON output across all your endpoints, making it simple for your frontend (React, Vue, mobile apps) to plug in one standard pattern.

---

## 🚀 Installation

```bash
composer require augmentmy/laravel-query-pagination
```

The service provider is auto‑discovered.

### Requirements

- PHP `>= 8.1`
- Laravel `10.x` or `11.x`
- Eloquent ORM (`illuminate/database`)

---

## 🔌 Unified Usage

The package now exposes ONE primary class:

- `PaginatedQuery`  
  - builds the query (search, filters, sorting, pagination)  
  - can return the raw paginator via `paginate()`  
  - or return JSON API response via `toResponse()`

### Minimal Controller Example

```php
use AugmentMy\LaravelQueryPagination\PaginatedQuery;
use Illuminate\Http\Request;
use App\Models\User;

class UserController
{
    public function index(Request $request)
    {
        return PaginatedQuery::make(
            baseQuery: User::query(),
            searchable: ['name', 'email'],
            filterable: ['role', 'status'],
            sortable: ['name', 'created_at'],
            defaultSort: 'created_at',
            defaultSortDir: 'desc',
        )->toResponse($request);
    }
}
```

This is the cleanest form.  
No need to manually call `PaginatedResponse`.

---

## ⚙️ Constructor Signature

```php
PaginatedQuery::make(
    Builder $baseQuery,
    array $searchable = [],
    array $filterable = [],
    array $sortable = [],
    ?string $defaultSort = null,
    string $defaultSortDir = 'asc',
)
```

### Parameters

| Param             | Description |
|------------------|-------------|
| `baseQuery`       | Any Eloquent query builder (`User::query()`, etc.) |
| `searchable`      | Columns used when `search=` is provided |
| `filterable`      | Allowed filter fields (`filters[field]=value`) |
| `sortable`        | Allowed columns for `sort_by` |
| `defaultSort`     | Fallback sort column |
| `defaultSortDir`  | `"asc"` or `"desc"` |

---

## 🔍 Supported Query Parameters

| Param                | Example              | Description |
|----------------------|----------------------|-------------|
| `page`               | `page=3`             | Page number (1‑based) |
| `per_page`           | `per_page=50`        | Items per page (clamped 1–100) |
| `search`             | `search=john`        | Searches all `$searchable` fields |
| `filters[key]`       | `filters[role]=admin`| Per-field filters |
| `sort_by`            | `sort_by=name`       | Sorting field |
| `sort_dir`           | `sort_dir=desc`      | Sorting direction |

---

## 📦 JSON Response Format

All endpoints return:

```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 25,
    "per_page": 25,
    "last_page": 6,
    "total": 143,
    "path": "https://api.example.com/api/users",
    "first_page_url": "...",
    "last_page_url": "...",
    "next_page_url": "...",
    "prev_page_url": null,
    "has_more_pages": true,
    "on_first_page": true
  }
}
```

These values come directly from Laravel’s `LengthAwarePaginator`.

---

## 🔧 Raw paginator access (optional)

If you ever need the paginator directly:

```php
$paginator = PaginatedQuery::make(
    User::query(),
    searchable: ['name']
)->paginate($request);
```

You still get the `LengthAwarePaginator` object exactly as Laravel returns it.

---

## 🧪 Testing (for contributors)

This project uses **Orchestra Testbench** to bootstrap a minimal Laravel environment.

Run:

```bash
composer install
vendor/bin/phpunit
```

Tests use:

- In‑memory SQLite (`:memory:`)
- Temporary table creation
- Simple models to validate search/filter/sort/pagination

---

## 🗺️ Roadmap

- Range-based filtering (`from`, `to`)
- Relationship filters (`filters[role.name]`)
- Configurable operators
- Controller trait: `HasPaginatedIndex`
- Global config: max per_page, default sorting rules

---

## 📄 License

MIT License. See `LICENSE` for details.
