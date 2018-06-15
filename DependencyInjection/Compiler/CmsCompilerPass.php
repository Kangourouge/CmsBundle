<?php
namespace KRG\CmsBundle\DependencyInjection\Compiler;

use KRG\CmsBundle\Form\FilterRegistry;
use KRG\CmsBundle\Routing\Generator\Dumper\PhpGeneratorDumper;
use KRG\CmsBundle\Routing\Generator\UrlGenerator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class CmsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->setRouterOptions($container);
        $this->populateFilterRegistry($container);
    }

    public function populateFilterRegistry(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition(FilterRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(FilterRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('krg.cms.filter');
        foreach ($taggedServices as $className => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('add', [
                    $className,
                    $attributes['alias'] ?? $className,
                    $attributes['template'] ?? 'KRGCmsBundle:Filter:fallback.html.twig',
                    isset($attributes['handler']) ? new Reference($attributes['handler']) : null,
                ]);
            }
        }
    }

    public function setRouterOptions(ContainerBuilder $container, $id = 'router.default')
    {
        if (false === $container->hasDefinition($id)) {
            return;
        }

        $router = $container->getDefinition($id);
        $options = $router->getArgument(2);
        if (is_array($options)) {
            $options['generator_class'] = UrlGenerator::class;
            $options['generator_base_class'] = UrlGenerator::class;
            $options['generator_dumper_class'] = PhpGeneratorDumper::class;
//             $options['generator_cache_class'] = null; // No cache for debug
            $router->setArgument(2, $options);
        }
    }
}
