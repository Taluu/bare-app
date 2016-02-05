<?php
namespace Bare;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

use Symfony\Bundle\MonologBundle\MonologBundle;
use Sensio\Bundle\DistributionBundle\SensioDistributionBundle;

class AppKernel extends Kernel
{
    const VERSION = '0.0.1';

    public function registerBundles()
    {
        $bundles = [
            new MonologBundle,
            new BareApp
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new SensioDistributionBundle;
        }

        return $bundles;
    }

    public function getName()
    {
        return 'bare_app';
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config.yml');
    }
}
