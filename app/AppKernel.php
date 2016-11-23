<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle()
        ];

        $this->registerProjectBundles($bundles);

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/' . $this->getEnvironment() . '/config.yml');
    }

    /**
     * Register bundles from project
     *
     * @param array &$bundles
     */
    private function registerProjectBundles(&$bundles)
    {
        $searchPath = __DIR__ . '/../src';
        $finder     = new \Symfony\Component\Finder\Finder();
        $finder->files()
            ->in($searchPath)
            ->name('*Bundle.php');

        foreach ($finder as $file) {
            $path      = str_replace('.php', '', substr($file->getRealPath(), strrpos($file->getRealPath(), 'src') + 4));
            $parts     = explode('/', $path);
            $class     = array_pop($parts);
            $namespace = implode('\\', $parts);
            $class     = $namespace . '\\' . $class;
            $bundles[] = new $class();
        }
    }
}
