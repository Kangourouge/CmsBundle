<?php
namespace KRG\SeoBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PageFormCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('krg_seo.form.registry')) {
            return;
        }

        $definition = $container->findDefinition('krg_seo.form.registry');
        $taggedServices = $container->findTaggedServiceIds('seo.page.form');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall('add', array(
                    new Reference($id),
                    $attributes['alias'],
                    $attributes['route'],
                ));
            }
        }
    }
}
