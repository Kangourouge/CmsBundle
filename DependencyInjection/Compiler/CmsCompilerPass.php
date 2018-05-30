<?php
namespace KRG\CmsBundle\DependencyInjection\Compiler;

use KRG\CmsBundle\Form\FilterRegistry;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class CmsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(FilterRegistry::class)) {
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
}
