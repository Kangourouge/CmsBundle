<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\BlockInterface;
use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use KRG\CmsBundle\Routing\UrlResolver;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class UrlDataTransformer implements DataTransformerInterface
{
    /** @var UrlResolver */
    protected $urlResolver;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var RouterInterface */
    protected $router;

    public function __construct(UrlResolver $urlResolver, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->urlResolver = $urlResolver;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function transform($value)
    {
        if ($value === null) {
            return null;
        }

        // Handle JSON value (translations)
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (isset($value['url'])) {
            $seo = $this->entityManager->getRepository(SeoInterface::class)->findOneBy(['url' => $value['url']]);

            if ($seo) {
                $page = $this->entityManager->getRepository(PageInterface::class)->findOneBy(['seo' => $seo]);
                $filter = $this->entityManager->getRepository(FilterInterface::class)->findOneBy(['seo' => $seo]);

                if ($page ?? $filter) {
                    return ['related' => self::getRelatedIdentifier($page ?? $filter)];
                }
            } else {
                try {
                    $this->router->getRouteCollection()->get($value['name']);

                    return ['related' => self::getTypeIdentifier('route', $value['name'])];
                } catch (\Exception $exception) {
                }
            }
        }

        return ['url' => $value['url'] ?? null];
    }

    public function reverseTransform($value)
    {
        if ($value === null) {
            return [];
        }


        if (isset($value['related'])) {
            list($type, $id) = explode('-', $value['related']);

            if ($this->entityManager->getMetadataFactory()->hasMetadataFor($type)) {
                $metadata = $this->entityManager->getMetadataFactory()->getMetadataFor($type);
                if ($metadata->hasAssociation('seo')) {
                    $entity = $this->entityManager->getRepository($metadata->getName())->find($id);
                    $value['url'] = $entity->getSeo()->getUrl();
                }
            } elseif ($type === 'route') {
                try {
                    $value['url'] = $this->router->getRouteCollection()->get($id)->getPath();
                } catch (\Exception $exception) {
                }
            }
        }

        $routeInfo = [
            'url'    => $value['url'],
            'name'   => null,
            'params' => [],
        ];

        try {
            $routeInfo = array_merge($routeInfo, $this->urlResolver->resolve($value['url']));
        } catch (ResourceNotFoundException $exception) {
        } catch (\Exception $exception) {
            throw new TransformationFailedException();
        }

        return $routeInfo;
    }

    static public function getRelatedIdentifier(BlockInterface $block)
    {
        return self::getTypeIdentifier(get_class($block), $block->getId());
    }

    static public function getTypeIdentifier($type, $name)
    {
        return $type.'-'.$name;
    }
}
