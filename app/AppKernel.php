<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    /**
     * store basic directory path
     *
     * @var string
     */
    protected static $_basePath;

    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),
            new Acme\DemoBundle\AcmeDemoBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * rewrite base application cache directory
     *
     * @return string
     */
    public function getCacheDir()
    {
        return self::getBasePath() . 'var/cache/' . $this->environment;
    }

    /**
     * rewrite base application log directory
     *
     * @return string
     */
    public function getLogDir()
    {
        return self::getBasePath() . 'var/log';
    }

    /**
     * return basic application directory
     * before check that directory is set and store it in static variable
     *
     * @return string
     */
    public static function getBasePath()
    {
        if (!self::$_basePath) {
            self::$_basePath = preg_replace('#app/AppKernel\.php$#', '', __FILE__);
        }

        return self::$_basePath;
    }
}
