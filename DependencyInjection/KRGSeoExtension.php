<?php

namespace KRG\SeoBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class KRGSeoExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->injectParameter('title_prefix', $container, $config);
        $this->injectParameter('title_suffix', $container, $config);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param $name
     * @param ContainerBuilder $container
     * @param $config
     */
    private function injectParameter($name, ContainerBuilder $container, $config)
    {
        $container->setParameter(sprintf('krg_seo.%s', $name), isset($config[$name]) ? $config[$name] : null);
    }

    public function getAlias()
    {
        return 'krg_seo';
    }
}
