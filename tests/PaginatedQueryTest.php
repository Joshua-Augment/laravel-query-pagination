<?php

namespace AugmentMy\LaravelQueryPagination\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use AugmentMy\LaravelQueryPagination\PaginatedQuery;

class User extends Model
{
    protected $fillable = ['name', 'email', 'role'];
}

class PaginatedQueryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test table
        $this->app['db']->connection()->getSchemaBuilder()->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('role')->nullable();
            $table->timestamps();
        });

        User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'admin']);
        User::create(['name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'user']);
    }

    /** @test */
    public function test_it_can_search()
    {
        $query = new PaginatedQuery(
            User::query(),
            searchable: ['name']
        );

        $req = request()->merge(['search' => 'Jane']);

        $res = $query->fromRequest($req);

        $this->assertCount(1, $res->items());
        $this->assertEquals('Jane Smith', $res->items()[0]->name);
    }

    /** @test */
    public function test_it_can_filter()
    {
        $query = new PaginatedQuery(
            User::query(),
            searchable: [],
            filterable: ['role']
        );

        $req = request()->merge(['filters' => ['role' => 'admin']]);

        $res = $query->fromRequest($req);

        $this->assertCount(1, $res->items());
        $this->assertEquals('admin', $res->items()[0]->role);
    }

    /** @test */
    public function test_it_can_sort()
    {
        $query = new PaginatedQuery(
            User::query(),
            sortable: ['name']
        );

        $req = request()->merge([
            'sort_by' => 'name',
            'sort_dir' => 'desc'
        ]);

        $res = $query->fromRequest($req);

        $this->assertEquals('John Doe', $res->items()[0]->name);
    }
}
