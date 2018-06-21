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

class UrlDataTransformer implements DataTransformerInterface
{
    /** @var UrlResolver */
    protected $urlResolver;

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(UrlResolver $urlResolver, EntityManagerInterface $entityManager)
    {
        $this->urlResolver = $urlResolver;
        $this->entityManager = $entityManager;
    }

    public function transform($value)
    {
        if ($value === null) {
            return null;
        }

        if (isset($value['url']) && $seo = $this->entityManager->getRepository(SeoInterface::class)->findOneBy(['url' => $value['url']])) {
            $page = $this->entityManager->getRepository(PageInterface::class)->findOneBy(['seo' => $seo]);
            $filter = $this->entityManager->getRepository(FilterInterface::class)->findOneBy(['seo' => $seo]);

            if ($page ?? $filter) {
                return ['block' => self::getBlockIdentifier($page ?? $filter)];
            }
        }

        return ['url' => $value['url'] ?? null];
    }

    public function reverseTransform($value)
    {
        if ($value === null) {
            return [];
        }

        if (isset($value['block'])) {
            list($className, $id) = explode('-', $value['block']);

            $metadata = $this->entityManager->getClassMetadata($className);
            if ($metadata->hasAssociation('seo')) {
                $entity = $this->entityManager->getRepository($metadata->getName())->find($id);
                $value['url'] = $entity->getSeo()->getUrl();
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

    static public function getBlockIdentifier(BlockInterface $block)
    {
        return sprintf('%s-%d', get_class($block), ($block)->getId());
    }
}
