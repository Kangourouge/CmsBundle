<?php
namespace KRG\SeoBundle\DependencyInjection\Compiler;

use KRG\SeoBundle\Form\SeoFormRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SeoFormCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(SeoFormRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(SeoFormRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('seo.form');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall('add', [
                    $id,
                    $attributes['alias'],
                    $attributes['template'],
                    new Reference($attributes['handler']),
                ]);
            }
        }
    }
}
