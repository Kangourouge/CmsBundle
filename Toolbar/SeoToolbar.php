<?php

namespace KRG\CmsBundle\Toolbar;

use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use KRG\EasyAdminExtensionBundle\Toolbar\ToolbarInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Templating\EngineInterface;

class SeoToolbar implements ToolbarInterface
{
    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var RequestStack */
    protected $requestStack;

    /** @var EngineInterface */
    protected $templating;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, RequestStack $requestStack, EngineInterface $templating)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->requestStack = $requestStack;
        $this->templating = $templating;
    }

    public function render()
    {
        if ($this->authorizationChecker->isGranted('ROLE_SEO')) {
            $request = $this->requestStack->getMasterRequest();

            /** @var $seo SeoInterface */
            $seo = null;
            if ($request->attributes->has('_seo')) {
                $seo = $request->attributes->get('_seo');
            }

            /** @var $filter FilterInterface */
            $filter = null;
            if ($request->attributes->has('filter')) {
                if ($request->attributes->get('filter') instanceof FilterInterface) {
                    $filter = $request->attributes->get('filter');
                }
            }

            return $this->templating->render('@KRGCms/toolbar/seo.html.twig', [
                'seo'    => $seo,
                'filter' => $filter,
            ]);
        }

        return null;
    }
}
