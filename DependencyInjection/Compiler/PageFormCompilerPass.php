<?php
namespace KRG\SeoBundle\DependencyInjection\Compiler;

use KRG\SeoBundle\Form\SeoFormRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PageFormCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(SeoFormRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(SeoFormRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('seo.page.form');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall('add', [
                    new Reference($id),
                    $attributes['alias'],
                    $attributes['route'],
                ]);
            }
        }

    }
}
