<?php

namespace JoelButcher\EloquentRepositories\Contracts;

/**
 * @template TKey of array-key
 * @template TValue
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
interface Repository
{
    /**
     * Dynamically forward any calls to the underlying query builder instance.
     *
     * @param  string  $method
     * @param  array<TKey, TValue>  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed;
}
