# Eloquent Repositories

<a href="https://github.com/joelbutcher/eloquent-repositories/actions">
    <img src="https://github.com/joelbutcher/eloquent-repositories/workflows/tests/badge.svg" alt="Build Status">
</a>
<a href="https://packagist.org/packages/joelbutcher/eloquent-repositories">
    <img src="https://img.shields.io/packagist/dt/joelbutcher/eloquent-repositories" alt="Total Downloads">
</a>
<a href="https://packagist.org/packages/joelbutcher/eloquent-repositories">
    <img src="https://img.shields.io/packagist/v/joelbutcher/eloquent-repositories" alt="Latest Stable Version">
</a>
<a href="https://packagist.org/packages/joelbutcher/eloquent-repositories">
    <img src="https://img.shields.io/packagist/l/joelbutcher/eloquent-repositories" alt="License">
</a>

Eloquent Repositories is a light-weight solution to add the repository pattern (built on top of Eloquent) to your Laravel project.

## Requirements

This package requires Laravel 8 or higher and PHP 7.4 or higher.

## Getting Started

Add this package to your project using Composer:

```shell
composer require joelbutcher/eloquent-repositories
```

## Creating a repository

The quickest way to create a repository is to use the `make:repository` command.
This command accepts the name of the repository you with to create:

```shell
php artisan make:repository PostRepository
```

This command will create a new repository in the `app/Repositories` directory in your Laravel application.

You may also optionally append the `--model=` option to specify the repository should be created
for the given model. (The model needs to have been created prior to running this command.) 

```shell
php artisan make:repository PostRepository --model=Post
php artisan make:repository PostRepository -m Post
```

You may also use the `test` or `pest` options to create PHPUnit or Pest test files for the repository.

### Manual Creation

Of course, you may also create a repository manually

```php
use App\Models\Post;
use JoelButcher\EloquentRepositories\Repository;
use Illuminate\Database\Eloquent\Model;

class PostRepository extends Repository
{
    protected static function model(): string
    {
        return Post::class;
    }

    public function firstForSlug(string $slug): ?Model
    {
        return $this->where('slug', '=', $slug)->first();
    }
}
```

## Using a repository

To use a repository, you may "inject" it into any class that requires it:

```php
class UpdatePost
{
    public function __construct(
        private readonly PostValidator $validator,
        private readonly PostRepository $repository
    ) {
    }
    
    public function update(array $data): void
    {
        $this->validator->validate($data);
        
        $this->repository->upsert($data, ['slug'], ['slug']);
    }
}
```

## Interface Binding

You may decide to bind a repository to an interface, you may do so by implementing this interface
in your repository and then binding the concrete repository implementation to the service container:

```php
class UserRepository extends Repository implements UserRepositoryContract
{
    //
} 


class MyServiceProvider extends ServiceProvider
{
    public function boot(): 
    {
        $this->app->bind(UserRepositoryContract::class, UserRepository::::class)
    }
}
```

## Contributing

Thank you for considering contributing to Eloquent Repositories! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the community is welcoming to all, please review and abide by the [Code of Conduct](.github/CODE_OF_CONDUCT.md).


## License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).
