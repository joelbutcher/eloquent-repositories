<?php

namespace JoelButcher\EloquentRepositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\ForwardsCalls;
use JoelButcher\EloquentRepositories\Contracts\Repository as RepositoryContract;
use RuntimeException;

/**
 * @template TKey of array-key
 * @template TValue
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @implements RepositoryContract<class-string, TValue, TModel>
 * @mixin Builder<Model|TModel>
 */
abstract class Repository implements RepositoryContract
{
    use ForwardsCalls;

    /**
     * The underlying query builder instance.
     *
     * @var Builder<Model|TModel>
     */
    protected Builder $query;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->newQuery();
    }

    /**
     * Return the name of the repository's corresponding model.
     *
     * @return class-string<Model|TModel>
     *
     * @throws RuntimeException
     */
    protected static function model(): string
    {
        throw new RuntimeException('Repository does not implement the \'model\' method.');
    }

    /**
     * Instantiate a new query builder on the repository.
     *
     * @return Builder<Model|TModel>
     */
    protected function newQuery(): Builder
    {
        $model = $this->model();

        $this->query = (new $model())->newQuery();

        return $this->query;
    }

    /**
     * Dynamically forward any calls to the underlying query builder instance.
     *
     * @param  string  $method
     * @param  array<TKey, TValue>  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->forwardCallTo($this->newQuery(), $method, $parameters);
    }
}
