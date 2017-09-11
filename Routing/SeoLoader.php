<?php

namespace KRG\SeoBundle\Routing;

use KRG\SeoBundle\Entity\SeoInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Load custom routes from KRG SEOBUNDLE
 */
class SeoLoader extends Loader
{
    /**
     * @var bool
     */
    private $loaded = false;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "Seo" loader twice');
        }

        $className = $this->entityManager->getClassMetadata(SeoInterface::class)->getName();
        $seoRepository = $this->entityManager->getRepository($className);
        $seoEntries = $seoRepository->findBy(array(
            'enabled' => true
        ));

        $routes = new RouteCollection();

        $this->normalizer->setCircularReferenceHandler(function($object) {
            return $object->getId();
        });

        $serializer = new Serializer(array($this->normalizer), array($this->encoder));

        /* @var $seo SeoInterface */
        foreach ($seoEntries as $seo) {
            $route = new Route($seo->getUrl());
            $route->setSeoClass($className);
            $route->setSeo($serializer->serialize($seo, 'json'));
            $routes->add($seo->getUid(), $route);
        }

        $this->loaded = true;

        return $routes;
    }

    /* */

    public function supports($resource, $type = null)
    {
        return 'seo' === $type;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param EncoderInterface $encoder
     */
    public function setEncoder($encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param NormalizerInterface $normalizer
     */
    public function setNormalizer($normalizer)
    {
        $this->normalizer = $normalizer;
    }
}
