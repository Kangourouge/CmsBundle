<?php

namespace KRG\SeoBundle\Twig;

use KRG\SeoBundle\Entity\SeoInterface;
use KRG\SeoBundle\Entity\SeoPageInterface;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

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

    public function getSeoHead(\Twig_Environment $environment)
    {
        if ($this->request === null) {
            return;
        }

        /* @var $seo SeoInterface */
        $seo = $this->request->get('_seo');
        if ($seo === null) {
            return;
        }

        // Get usefull parameters from the request
        $params = array_filter($this->request->attributes->all(), function ($key) {
            return substr($key, 0, 1) !== '_';
        }, ARRAY_FILTER_USE_KEY);

        $twig = new \Twig_Environment(new \Twig_Loader_String());

        $data = array(
            'metaTitle'       => null,
            'metaDescription' => null,
            'metaRobots'      => null,
            'ogTitle'         => null,
            'ogDescription'   => null,
            'ogImage'         => null,
        );

        foreach($data as $key => &$value) {
            $getter = 'get' . ucfirst($key);
            if (method_exists($seo, $getter) && $template = call_user_func(array($seo, $getter))) {
                $value = $twig->render($template, $params);
            }
        }
        unset($value);

        return $environment->render('KRGSeoBundle:Seo:head.html.twig', $data);
    }

    public function getSeoAdmin(\Twig_Environment $environment)
    {
        if ($this->request === null) {
            return;
        }

        if (false === $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') &&
            false === $this->authorizationChecker->isGranted('ROLE_SEO')) {
            return;
        }

        /* @var $seo SeoInterface */
        $seo = $this->request->get('_seo');

        $formName = null;
        /* @var $seoPage SeoPageInterface */
        if ($seo && $seo->getSeoPage() && $seo->getSeoPage()->getFormType()) {
            $form = $this->formFactory->create($seo->getSeoPage()->getFormType());
            $formName = $form->getName();
        }

        return $environment->render('KRGSeoBundle:Seo:front.html.twig', array(
            'seo'      => $this->request->get('_seo'),
            'formName' => $formName
        ));
    }

    public function getFunctions()
    {
        return array(
            'seo_head' => new \Twig_Function_Method($this, 'getSeoHead', array(
                'needs_environment' => true,
                'is_safe'           => array('html'),
            )),
            'seo_admin' => new \Twig_Function_Method($this, 'getSeoAdmin', array(
                'needs_environment' => true,
                'is_safe'           => array('html'),
            )),
        );
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'krg_seo_bundle';
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->request = $requestStack->getMasterRequest();
    }

    /**
     * @param TokenStorage $tokenStorage
     */
    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param AuthorizationChecker $authorizationChecker
     */
    public function setAuthorizationChecker($authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormFactory $formFactory
     */
    public function setFormFactory($formFactory)
    {
        $this->formFactory = $formFactory;
    }
}
