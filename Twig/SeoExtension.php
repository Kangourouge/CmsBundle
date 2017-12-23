<?php

namespace KRG\CmsBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use KRG\CmsBundle\Entity\SeoPageInterface;
use Doctrine\ORM\EntityManager;
use KRG\CmsBundle\KRGCmsBundle;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class SeoExtension extends \Twig_Extension
{
    /**
     * @var $entityManager EntityManager
     */
    private $entityManager;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var FormFactory
     */
    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, FormFactoryInterface $formFactory, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->formFactory = $formFactory;
        $this->request = $requestStack->getMasterRequest();
    }

    public function getSeoHead(\Twig_Environment $environment)
    {
        if ($this->request === null) {
            return null;
        }

        /* @var $seo SeoInterface */
        $seo = $this->request->get('_seo');
        if ($seo === null) {
            return null;
        }

        // Get usefull parameters from the request
        $params = array_filter($this->request->attributes->all(), function ($key) {
            return substr($key, 0, 1) !== '_';
        }, ARRAY_FILTER_USE_KEY);

        $twig = new \Twig_Environment(new \Twig_Loader_Array(array()));

        $data = array(
            'metaTitle'       => null,
            'metaDescription' => null,
            'metaRobots'      => null,
            'ogTitle'         => null,
            'ogDescription'   => null,
            'ogImage'         => null,
        );

        // Use twig environnement to bind {{ var }}
        foreach($data as $key => &$value) {
            $getter = 'get' . ucfirst($key);
            if (method_exists($seo, $getter)) {
                if ($input = call_user_func(array($seo, $getter))) {
                    $value = $twig->createTemplate($input)->render($params);
                }
            }
        }
        unset($value);

        return $environment->render('KRGCmsBundle:Seo:head.html.twig', $data);
    }

    public function getSeoAdmin(\Twig_Environment $environment)
    {
        if ($this->request === null) {
            return null;
        }

        if (false === $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') &&
            false === $this->authorizationChecker->isGranted(KRGCmsBundle::ROLE_SEO)) {
            return null;
        }

        /* @var $seo SeoInterface */
        $seo = $this->request->get('_seo');

        return $environment->render('KRGCmsBundle:Seo:front.html.twig', array(
            'seo' => $seo,
        ));
    }

    public function getSeoUrl($key)
    {
        /* @var $page PageInterface */
        $page = $this->entityManager->getRepository(PageInterface::class)->findBy([
            'enabled' => true,
            'key'     => $key,
        ]);

        if ($page) {
            return $page->getSeo()->getUrl();
        }

        return '#';
    }

    public function getFunctions()
    {
        return array(
            'seo_head' => new \Twig_SimpleFunction('seo_head', array($this, 'getSeoHead'), array(
                'needs_environment' => true,
                'is_safe'           => array('html'),
            )),
            'seo_admin' => new \Twig_SimpleFunction('seo_admin', array($this, 'getSeoAdmin'), array(
                'needs_environment' => true,
                'is_safe'           => array('html'),
            )),
            'seo_url' => new \Twig_SimpleFunction('seo_url', array($this, 'getSeoUrl'), array(
                'is_safe' => array('html'),
            )),
        );
    }
}
