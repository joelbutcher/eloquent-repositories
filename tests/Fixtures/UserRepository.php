<?php

namespace JoelButcher\EloquentRepositories\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use JoelButcher\EloquentRepositories\Repository;

/**
 * @template TKey of array-key
 * @template TValue
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends Repository<TKey, TValue, TModel>
 */
class UserRepository extends Repository
{
    /**
     * Return the name of the repository's corresponding model.
     *
     * @return class-string<Model|TModel>
     */
    protected static function model(): string
    {
        return User::class;
    }

    /**
     * @param  string  $email
     * @return Model|TModel|null
     */
    public function firstForEmail(string $email): ?Model
    {
        return $this->where('email', '=', $email)->first();
    }
}
