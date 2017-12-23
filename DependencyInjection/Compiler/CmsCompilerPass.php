<?php
namespace KRG\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use KRG\CmsBundle\Form\BlockFormRegistry;

class CmsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(BlockFormRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(BlockFormRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('krg.cms.form');

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
