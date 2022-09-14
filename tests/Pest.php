<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Illuminate\Database\Eloquent\Model;
use JoelButcher\EloquentRepositories\Tests\Fixtures\User;
use JoelButcher\EloquentRepositories\Tests\Fixtures\UserRepository;
use JoelButcher\EloquentRepositories\Tests\TestCase;

uses(TestCase::class)->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function password(string $password, array $options = []): string
{
    return password_hash($password, PASSWORD_BCRYPT, [
        'cost' => $options['rounds'] ?? 10,
    ]);
}

/**
 * @param  array  $attributes
 * @return array<array-key, User|UserRepository>
 */
function newUser(array $attributes = []): array
{
    $repository = new UserRepository();

    $user = $repository->create(array_merge([
        'name' => 'Joel Butcher',
        'email' => 'joel@joelbutcher.dev',
        'password' => password('password'),
    ], $attributes));

    return [$user, $repository];
}

function assertUser(Model $user, array $attributes = []): void
{
    expect($user)->toBeInstanceOf(User::class);

    foreach ($attributes as $key => $value) {
        expect($user->$key)->toEqual($value);
    }
}

function assertUserExists(Model $user): void
{
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->exists)->toBeTrue();
}

function assertUserNotExists(Model $user): void
{
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->exists)->toBeFalse();
}
