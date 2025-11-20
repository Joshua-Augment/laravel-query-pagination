# Laravel Query Pagination

A lightweight, framework-friendly helper for building **consistent, API-friendly pagination, sorting, filtering, and search** endpoints in Laravel.

Instead of re-writing `page`, `per_page`, `search`, `filters[...]`, and `sort_by` logic in every controller, this package provides:

- `PaginatedQuery` — applies search, filters, sorting, and pagination to any Eloquent query.
- `PaginatedResponse` — returns a unified JSON shape for all paginated endpoints.

This makes it easy for frontends (React, Vue, mobile apps, etc.) to rely on a **single API contract** across your backend.

---

## 🚀 Installation

```bash
composer require augmentmy/laravel-query-pagination
```

The service provider is auto-discovered by Laravel; no manual registration required.

### Requirements

- PHP `>= 8.1`
- Laravel `10.x` or `11.x`
- Eloquent ORM (`illuminate/database`)

---

## 🔌 HTTP API Contract

This package standardizes the query parameters and response structure of all paginated endpoints.

### **Query Parameters (client → server)**

| Param              | Type     | Description |
|--------------------|----------|-------------|
| `page`             | int      | 1-based page index. |
| `per_page`         | int      | Items per page (clamped between **1** and **100**). |
| `search`           | string   | Free-text search applied across configured columns. |
| `filters[key]`     | mixed    | Per-field filters, e.g. `filters[role]=admin`. |
| `sort_by`          | string   | Column name to sort by (must be whitelisted). |
| `sort_dir`         | string   | `"asc"` or `"desc"` (defaults to `"asc"`). |

### Example request

```
GET /api/users?page=2&per_page=25&search=john&filters[role]=admin&sort_by=name&sort_dir=asc
```

---

## 📦 Unified Response Structure

All controllers using this package should return:

```json
{
  "data": [
    { "id": 1, "name": "John Doe", "email": "john@example.com" }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 25,
    "last_page": 6,
    "total": 143
  }
}
```

This is produced using:

```php
return PaginatedResponse::fromPaginator($paginator);
```

---

## 🛠️ Usage Example

### Controller

```php
use App\Models\User;
use Illuminate\Http\Request;
use AugmentMy\LaravelQueryPagination\PaginatedQuery;
use AugmentMy\LaravelQueryPagination\PaginatedResponse;

class UserController
{
    public function index(Request $request)
    {
        $paginator = (new PaginatedQuery(
            baseQuery: User::query(),
            searchable: ['name', 'email'],
            filterable: ['role', 'status'],
            sortable: ['name', 'created_at'],
            defaultSort: 'created_at',
            defaultSortDir: 'desc',
        ))->fromRequest($request);

        return PaginatedResponse::fromPaginator($paginator);
    }
}
```

---

## ⚙️ `PaginatedQuery` Constructor

```php
new PaginatedQuery(
    Builder $baseQuery,
    array $searchable = [],
    array $filterable = [],
    array $sortable = [],
    ?string $defaultSort = null,
    string $defaultSortDir = 'asc',
)
```

### Parameter Breakdown

| Param           | Description |
|-----------------|-------------|
| `$baseQuery`     | Any Eloquent builder (e.g., `User::query()`). |
| `$searchable`    | Columns included in the text search. |
| `$filterable`    | Allowed filter fields (`filters[field]=value`). |
| `$sortable`      | Allowed columns for sorting. |
| `$defaultSort`   | Default sort column (if no `sort_by` specified). |
| `$defaultSortDir`| `"asc"` or `"desc"` (default). |

---

## 🔍 Search Behavior

If `search` is provided, it applies:

```sql
WHERE (column1 LIKE "%term%" OR column2 LIKE "%term%" ...)
```

Example:

```
GET /api/users?search=jane
```

With:

```php
searchable: ['name', 'email']
```

---

## 🧩 Filtering Behavior

Filters come from:

```
filters[field]=value
```

Example:

```
GET /api/users?filters[role]=admin&filters[status]=active
```

Only keys listed in `$filterable` are applied.

---

## ↕ Sorting Behavior

Request:

```
GET /api/users?sort_by=name&sort_dir=desc
```

Rules:

- `sort_by` must appear in the `$sortable` list.
- If `sort_by` missing, defaults apply.
- Any invalid sort direction becomes `"asc"`.

---

## 📄 Pagination Behavior

- `page` defaults to `1`
- `per_page` defaults to `15`
- `per_page` always clamped between **1** and **100**

Example:

```
GET /api/users?page=3&per_page=50
```

---

## 🧪 Testing (for contributors)

This package uses **Orchestra Testbench** to bootstrap a miniature Laravel environment.

Run:

```bash
composer install
vendor/bin/phpunit
```

Tests use:

- in-memory SQLite (`:memory:`)
- temporary `users` table
- simple model for verifying search/filter/sort/pagination

---

## 🗺️ Roadmap

- Range filter support (`filters[created_at][from]`, `filters[created_at][to]`)
- Relationship filtering (`filters[role.name]`)
- Config file for:
  - global max `per_page`
  - default sorting
  - allowed filter operators
- Controller trait `HasPaginatedIndex`

---

## 📄 License

MIT License. See the `LICENSE` file for details.
