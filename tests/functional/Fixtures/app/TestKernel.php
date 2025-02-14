<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    protected $configFile = 'config.yml';

    public function registerBundles(): array
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Maba\Bundle\WebpackBundle\MabaWebpackBundle(),
            new \Fixtures\Maba\Bundle\WebpackTestBundle\MabaWebpackTestBundle(),
            new \Fixtures\Maba\Bundle\WebpackAnotherTestBundle\MabaWebpackAnotherTestBundle(),
            new \Fixtures\Maba\Bundle\TestCommonsChunkBundle\MabaTestCommonsChunkBundle(),
        ];
    }

    /**
     * @param string $configFile
     */
    public function setConfigFile($configFile)
    {
        $this->configFile = 'config_' . $configFile . '.yml';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/' . $this->configFile);
    }
}