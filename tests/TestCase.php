<?php
namespace Czim\CmsAuthApi\Test;

use Czim\CmsAuth\Sentinel\Users\EloquentUser;
use Czim\CmsCore\Providers\CmsCoreServiceProvider;
use Czim\CmsCore\Support\Enums\Component;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    const USER_ADMIN_EMAIL    = 'admin@cms.com';
    const USER_ADMIN_PASSWORD = 'password';

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('cms-core.database.testing.driver', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('cms-core.database.testing.driver', 'testbench');

        // Prefix doesn't work in sqlite memory
        $app['config']->set('cms-core.database.prefix', '');

        // todo remove after fixing package config
        $app['config']->set('cms-modules.modules', []);

        // Load the CMS even when unit testing
        $app['config']->set('cms-core.testing', true);

        // Set up service providers for tests, excluding what is not part of this package
        $app['config']->set('cms-core.providers', [
            \Czim\CmsCore\Providers\ModuleManagerServiceProvider::class,
            \Czim\CmsCore\Providers\LogServiceProvider::class,
            \Czim\CmsCore\Providers\RouteServiceProvider::class,
            \Czim\CmsCore\Providers\MiddlewareServiceProvider::class,
            \Czim\CmsCore\Providers\MigrationServiceProvider::class,
            \Czim\CmsCore\Providers\ViewServiceProvider::class,
            \Czim\CmsAuth\Providers\CmsAuthServiceProvider::class,
            \Czim\CmsAuthApi\Providers\OAuthSetupServiceProvider::class,
            \Czim\CmsAuthApi\Providers\OAuth2ServerServiceProvider::class,
            \Czim\CmsAuthApi\Providers\FluentStorageServiceProvider::class,
            \Czim\CmsCore\Providers\Api\CmsCoreApiServiceProvider::class,
            \Czim\CmsCore\Providers\Api\ApiRouteServiceProvider::class,
        ]);

        // Mock component bindings in the config
        $app['config']->set(
            'cms-core.bindings', [
                Component::BOOTCHECKER => $this->getTestBootCheckerBinding(),
                Component::CACHE       => \Czim\CmsCore\Core\Cache::class,
                Component::CORE        => \Czim\CmsCore\Core\Core::class,
                Component::MODULES     => \Czim\CmsCore\Modules\ModuleManager::class,
                Component::AUTH        => \Czim\CmsAuthApi\Auth\Authenticator::class,
                Component::API         => \Czim\CmsCore\Api\ApiCore::class,
                Component::ACL         => \Czim\CmsCore\Auth\AclRepository::class,
                Component::MENU        => \Czim\CmsCore\Menu\MenuRepository::class,
        ]);

        $app->register(CmsCoreServiceProvider::class);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Orchestra\Database\ConsoleServiceProvider::class
        ];
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /**
     * Sets up the database for testing. This includes migration and standard seeding.
     */
    protected function setUpDatabase()
    {
        $this->app['config']->set('cms-core.database.driver', 'testbench');

        $this->migrateDatabase()
             ->seedDatabase();
    }

    /**
     * @return $this
     */
    protected function migrateDatabase()
    {
        // Note that although this will set up the migrated tables with the
        // prefix set by the CMS config, this will NOT use the cms:migrate
        // artisan context, so the migrations table will not be prefixed.

        $this->loadMigrationsFrom([
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__ . '/../migrations/oauth'),
        ]);


        // These migrations are duplicated (for testing only) from the
        // 'parent' CMS auth component package.

        $this->loadMigrationsFrom([
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__ . '/../migrations/test/'),
        ]);

        return $this;
    }

    /**
     * Seeds the database with standard testing content.
     */
    protected function seedDatabase()
    {
        // Set up a standard admin / superuser
        $admin = new EloquentUser([
            'email'      => static::USER_ADMIN_EMAIL,
            'password'   => \Hash::make(static::USER_ADMIN_PASSWORD),
            'last_name'  => 'Admin',
            'first_name' => 'Super',
        ]);

        $admin->is_superadmin = true;
        $admin->save();
    }

    /**
     * @return string
     */
    protected function getTestBootCheckerBinding()
    {
        return \Czim\CmsCore\Core\BootChecker::class;
    }

    /**
     * @param string $table
     * @return string
     */
    protected function prefixTable($table)
    {
        // No prefix, due to sqlite memory driver
        return $table;
    }

    /**
     * @param array|string $realpath
     */
    protected function loadMigrationsFrom($realpath)
    {
        $options = is_array($realpath) ? $realpath : ['--realpath' => $realpath];

        $this->artisan('migrate', $options);

        $this->app[ConsoleKernel::class]->setArtisan(null);
    }

}
