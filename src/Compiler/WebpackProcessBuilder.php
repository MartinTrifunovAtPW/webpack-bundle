<?php

namespace Maba\Bundle\WebpackBundle\Compiler;

use Maba\Bundle\WebpackBundle\Config\WebpackConfig;
use Symfony\Component\Process\Exception\RuntimeException as ProcessRuntimeException;
use Symfony\Component\Process\Process;

class WebpackProcessBuilder
{
    const DASHBOARD_MODE_ENABLED_ALWAYS = 'enabled_always';
    const DASHBOARD_MODE_ENABLED_ON_DEV_SERVER = 'enabled_on_dev_server';
    const DASHBOARD_MODE_DISABLED = 'disabled';

    private $workingDirectory;
    private $disableTty;
    private $webpackExecutable;
    private $webpackArguments;
    private $devServerExecutable;
    private $devServerArguments;
    private $dashboardExecutable;
    private $dashboardMode;

    public function __construct(
        $workingDirectory,
        $disableTty,
        array $webpackExecutable,
        array $webpackArguments,
        array $devServerExecutable,
        array $devServerArguments,
        array $dashboardExecutable,
        $dashboardMode
    ) {
        $this->workingDirectory = $workingDirectory;
        $this->disableTty = $disableTty;
        $this->webpackExecutable = $webpackExecutable;
        $this->webpackArguments = $webpackArguments;
        $this->devServerExecutable = $devServerExecutable;
        $this->devServerArguments = $devServerArguments;
        $this->dashboardExecutable = $dashboardExecutable;
        $this->dashboardMode = $dashboardMode;
    }

    public function buildWebpackProcess(WebpackConfig $config)
    {
        $arguments = array_merge(
            $this->webpackExecutable,
            ['--config', $config->getConfigPath()],
            $this->webpackArguments
        );
        $environment = [];

        if ($this->dashboardMode === self::DASHBOARD_MODE_ENABLED_ALWAYS && $this->isTtyAvailable()) {
            $this->addDashboard($arguments, $environment);
        }

        $process = $this->buildProcess($arguments, $environment);
        $process->setTimeout(3600);

        return $process;
    }

    public function buildDevServerProcess(WebpackConfig $config)
    {
        $arguments = array_merge(
            $this->devServerExecutable,
            ['--config', $config->getConfigPath()],
            $this->devServerArguments
        );
        $environment = ['WEBPACK_MODE' => 'watch'];

        $dashboardEnabled = in_array($this->dashboardMode, [
            self::DASHBOARD_MODE_ENABLED_ALWAYS,
            self::DASHBOARD_MODE_ENABLED_ON_DEV_SERVER,
        ], true);

        if ($dashboardEnabled && $this->isTtyAvailable()) {
            $this->addDashboard($arguments, $environment);
        }

        $process = $this->buildProcess($arguments, $environment);
        $process->setTimeout(0);

        return $process;
    }

    private function addDashboard(&$arguments, &$environment)
    {
        $arguments = array_merge(
            $this->dashboardExecutable,
            ['--'],
            $arguments
        );
        $environment = ['WEBPACK_DASHBOARD' => 'enabled'] + $environment;
    }

    private function buildProcess($arguments, $environment)
    {
        $process = new Process($arguments);
        $process->setEnv($environment);
        $process->setWorkingDirectory($this->workingDirectory);
//        $process->inheritEnvironmentVariables();

        $this->configureTty($process);

        return $process;
    }

    private function isTtyAvailable()
    {
        $process = new Process(['ls']);
        $this->configureTty($process);
        return $process->isTty();
    }

    private function configureTty(Process $process)
    {
        if ($this->disableTty) {
            return;
        }

        try {
            $process->setTty(true);
        } catch (ProcessRuntimeException $exception) {
            // thrown if TTY is not available - just ignore
        }
    }
}