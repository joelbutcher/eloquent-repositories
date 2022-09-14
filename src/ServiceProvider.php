<?php

namespace JoelButcher\EloquentRepositories;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JoelButcher\EloquentRepositories\Console\RepositoryMakeCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array<string, class-string>
     */
    protected array $commands = [
        'RepositoryMake' => RepositoryMakeCommand::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerCommands($this->commands);
    }

    /**
     * Register the given commands.
     *
     * @param  array<string, class-string>  $commands
     * @return void
     */
    private function registerCommands(array $commands): void
    {
        foreach (array_keys($commands) as $command) {
            $this->{"register{$command}Command"}();
        }

        $this->commands(array_values($commands));
    }

    /**
     * Register the command.
     *
     * @return void
     */
    public function registerRepositoryMakeCommand(): void
    {
        $this->app->singleton(RepositoryMakeCommand::class);
    }
}
