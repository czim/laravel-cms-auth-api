<?php
namespace Czim\CmsAuthApi\Providers;

use Czim\CmsAuthApi\Console\Commands\CreateOAuthClient;
use Czim\CmsCore\Contracts\Core\CoreInterface;
use Czim\CmsCore\Support\Enums\Component;
use Illuminate\Support\ServiceProvider;

/**
 * Class OAuthSetupServiceProvider
 *
 * Provider for setting up the App for OAuth support. Register this even
 * when not running an API request, if you want to use OAuth for the CMS API.
 */
class OAuthSetupServiceProvider extends ServiceProvider
{

    /**
     * @var CoreInterface
     */
    protected $core;


    public function boot()
    {
    }

    public function register()
    {
        $this->core = app(Component::CORE);

        $this->registerCommands()
             ->publishMigrations();
    }

    /**
     * Register OAuth CMS commands
     *
     * @return $this
     */
    protected function registerCommands()
    {
        $this->app->singleton('cms.commands.api.oauth-client-create', CreateOAuthClient::class);

        $this->commands([
            'cms.commands.api.oauth-client-create',
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function publishMigrations()
    {
        $this->publishes([
            realpath(dirname(__DIR__) . '/../../migrations/oauth') => $this->getMigrationPath(),
        ], 'migrations');

        return $this;
    }

    /**
     * @return string
     */
    protected function getMigrationPath()
    {
        return database_path($this->core->config('database.migrations.path'));
    }

}
