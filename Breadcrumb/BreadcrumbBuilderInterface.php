<?php

namespace KRG\CmsBundle\Breadcrumb;

use Symfony\Component\HttpFoundation\Request;

interface BreadcrumbBuilderInterface
{
    public function build(Request $request);
}
