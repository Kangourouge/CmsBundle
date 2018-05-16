<?php
namespace KRG\CmsBundle\DependencyInjection\Compiler;

use KRG\CmsBundle\Form\FilterRegistry;
use KRG\CmsBundle\Routing\RoutingLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CmsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(FilterRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(FilterRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('krg.cms.filter');
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall('add', [
                    $id,
                    $attributes['alias'] ?? $id,
                    $attributes['template'] ?? 'KRGCmsBundle:Block:framed_form.html.twig',
                    isset($attributes['handler']) ? new Reference($attributes['handler']) : null,
                ]);
            }
        }
    }
}
