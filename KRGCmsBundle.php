<?php

namespace KRG\CmsBundle;

use KRG\CmsBundle\DependencyInjection\Compiler\CmsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KRGCmsBundle extends Bundle
{
    const ROLE_SEO = 'ROLE_SEO';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CmsCompilerPass());
    }
}
