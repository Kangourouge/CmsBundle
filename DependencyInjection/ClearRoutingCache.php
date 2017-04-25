<?php

namespace KRG\SeoBundle\DependencyInjection;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Router;

class ClearRoutingCache
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Kernel
     */
    private $kernel;

    public function exec()
    {
        $cacheDir = $this->kernel->getCacheDir();

        foreach (array('matcher_cache_class', 'generator_cache_class') as $option) {
            $className = $this->router->getOption($option);
            $cacheFile = $cacheDir . DIRECTORY_SEPARATOR . $className . '.php';
            $this->filesystem->remove($cacheFile);
        }

        $this->router->warmUp($cacheDir);
    }

    /**
     * @param Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param Kernel $kernel
     */
    public function setKernel($kernel)
    {
        $this->kernel = $kernel;
    }
}
