<?php

use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Expression;
use JoelButcher\EloquentRepositories\Repository;
use JoelButcher\EloquentRepositories\Tests\Fixtures\User;
use JoelButcher\EloquentRepositories\Tests\Fixtures\UserRepository;

it('throws if the model method is not implemented', function () {
    expect(fn () => new class() extends Repository
    {
    })->toThrow(
        RuntimeException::class,
        'Repository does not implement the \'model\' method.'
    );
});

it('can save a new record and return the instance.', function () {
    $repository = new UserRepository();

    $user = $repository->create($attributes = [
        'name' => 'Joel Butcher',
        'email' => 'joel@joelbutcher.dev',
        'password' => password('password'),
    ]);

    assertUser($user, $attributes);
    assertUserExists($user);
});

it('throws when mass-assigning guarded properties to create a new record.', function () {
    expect(fn () => newUser(['guarded_test' => 'filled']))
        ->toThrow(MassAssignmentException::class, "Add fillable property to allow mass assignment on [JoelButcher\EloquentRepositories\Tests\Fixtures\User].");
});

it('can force mass assignment to create a new record.', function () {
    $repository = new UserRepository();

    $user = $repository->forceCreate($attributes = [
        'name' => 'Joel Butcher',
        'email' => 'joel@joelbutcher.dev',
        'password' => password('password'),
        'guarded_test' => 'filled',
    ]);

    assertUser($user, $attributes);
    assertUserExists($user);
    expect($user->guarded_test)->toEqual('filled');
});

it('can update a record.', function () {
    [$user, $repository] = newUser();

    $repository->update($updatedAttributes = [
        'name' => 'Taylor Otwell',
        'email' => 'taylor@laravel.com',
    ]);

    assertUser($user->fresh(), $updatedAttributes);
    assertUserExists($user);
});

it('can find a record by its primary key.', function () {
    [$user, $repository] = newUser();

    $found = $repository->find(1);

    expect($found->id)->toBe($user->id)
        ->and($found->name)->toBe($user->name)
        ->and($found->email)->toBe($user->email);
});

it('can find a record by its primary key and not throw.', function () {
    [$user, $repository] = newUser();

    $found = $repository->findOrFail(1);

    expect($found->id)->toBe($user->id)
        ->and($found->name)->toBe($user->name)
        ->and($found->email)->toBe($user->email);
});

it('can find a record by its primary key and throw.', function () {
    $repository = last(newUser());

    $class = User::class;

    expect(fn () => $repository->findOrFail(2))
        ->toThrow(ModelNotFoundException::class, "No query results for model [$class] 2");
});

it('can create a new model instance if no record is found for a given ID.', function () {
    $repository = last(newUser());

    $found = $repository->findOrNew(2);

    expect($found)->toBeInstanceOf(User::class)
        ->and($found->exists)->toBeFalse()
        ->and($found->name)->toBeNull();
});

it('throws if no closure is provided when no record is found for a given ID.', function () {
    $repository = last(newUser());

    expect(fn () => $repository->findOr(2))->toThrow(Error::class, 'Value of type null is not callable');
});

it('returns the result of a closure if no record is found for a given ID.', function (Closure $closure) {
    $repository = last(newUser());

    $result = $closure();

    expect($repository->findOr(id: 2, callback: $closure))->toBe($result);
})->with([
    fn () => fn () => true,
    fn () => fn () => false,
    fn () => fn () => 1,
    fn () => fn () => 2,
    fn () => fn () => 3,
    fn () => fn () => 4,
    fn () => fn () => 5,
    fn () => fn () => 10,
    fn () => fn () => 100,
]);

it('can create a new model instance if the first record is not found.', function () {
    $repository = new UserRepository();

    $user = $repository->firstOrNew($attributes = [
        'name' => 'Joel Butcher',
        'email' => 'joel@joelbutcher.dev',
        'password' => password('password'),
    ]);

    assertUser($user, $attributes);
    assertUserNotExists($user);
});

it('throws if closure is provided when failing to get the first record.', function () {
    $repository = new UserRepository();

    expect(fn () => $repository->firstOr())->toThrow(Error::class, 'Value of type null is not callable');
});

it('returns the result of a closure when failing to get the first record.', function (Closure $closure) {
    $repository = new UserRepository();

    $result = $closure();

    expect($repository->firstOr(callback: $closure))->toBe($result);
})->with([
    fn () => fn () => true,
    fn () => fn () => false,
    fn () => fn () => 1,
    fn () => fn () => 2,
    fn () => fn () => 3,
    fn () => fn () => 4,
    fn () => fn () => 5,
    fn () => fn () => 10,
    fn () => fn () => 100,
]);

it('returns the first result for the sole matching record', function () {
    $repository = last(newUser());

    // User 1
    assertUser($repository->where('email', '=', $email = 'joel@joelbutcher.dev')->sole(), [
        'id' => 1,
        'email' => $email,
    ]);

    // User 2
    $repository->create(['name' => 'Taylor Otwell', 'email' => 'taylor@laravel.com', 'password' => password('password')]);

    assertUser($repository->where('email', '=', $email = 'taylor@laravel.com')->sole(), [
        'id' => 2,
        'email' => $email,
    ]);
});

it('get a single column\'s value from the first result of a query if it\'s the sole matching record.', function () {
    $repository = last(newUser([
        'name' => 'Joel Butcher',
        'email' => 'joel@joelbutcher.dev',
    ]));

    expect($repository->soleValue('name'))->toBe('Joel Butcher')
        ->and($repository->soleValue('email'))->toBe('joel@joelbutcher.dev')
        ->and($repository->soleValue(new Expression('name')))->toBe('Joel Butcher')
        ->and($repository->soleValue(new Expression('email')))->toBe('joel@joelbutcher.dev');
});

it('can return a count of all records', function () {
    $repository = new UserRepository();

    $repository->insert([
        ['name' => 'Joel Butcher', 'email' => 'joel@joelbutcher.dev', 'password' => password('password')],
        ['name' => 'Taylor Otwell', 'email' => 'taylor@laravel.com', 'password' => password('password')],
    ]);

    expect($repository->count())->toBe(2);
});
