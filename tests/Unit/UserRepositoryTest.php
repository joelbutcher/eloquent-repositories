<?php

namespace JoelButcher\EloquentRepositories\Tests\Unit;

it('can retrieve the first record for a given email', function (string $email) {
    [$user, $repository] = newUser([
        'email' => $email,
    ]);

    assertUserExists($user);

    $first = $repository->firstForEmail($email);

    assertUser($first);
    expect($user->email)->toBe($first->email);
})->with([
    'joel@joelbutcher.dev',
    'taylor@laravel.com',
    'jess@laravel.com',
    'dries@laravel.com',
]);
