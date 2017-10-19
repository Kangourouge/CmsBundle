<?php

namespace KRG\SeoBundle\DependencyInjection;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

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

    public function __construct(RouterInterface $router, Filesystem $filesystem, KernelInterface $kernel)
    {
        $this->router = $router;
        $this->filesystem = $filesystem;
        $this->kernel = $kernel;
    }

    public function exec()
    {
        $cacheDir = $this->kernel->getCacheDir();

        foreach (array('matcher_cache_class', 'generator_cache_class') as $option) {
            $className = $this->router->getOption($option);
            $cacheFile = $cacheDir . DIRECTORY_SEPARATOR . $className . '.php';
            $this->filesystem->remove($cacheFile);
        }

        $cache = new FilesystemAdapter('seo');
        $ret = $cache->clear();

        $this->router->warmUp($cacheDir);
    }
}
