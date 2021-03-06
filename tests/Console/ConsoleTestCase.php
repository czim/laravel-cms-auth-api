<?php
namespace Czim\CmsAuthApi\Test\Console;

use App\Console\Kernel;
use Czim\CmsAuthApi\Test\TestCase;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;

abstract class ConsoleTestCase extends TestCase
{

    /**
     * Returns most recent artisan command output.
     *
     * @return string
     */
    protected function getArtisanOutput()
    {
        return $this->getConsoleKernel()->output();
    }

    /**
     * @return ConsoleKernelContract|Kernel
     */
    protected function getConsoleKernel()
    {
        return $this->app[ConsoleKernelContract::class];
    }

}
