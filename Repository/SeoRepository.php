<?php

namespace KRG\CmsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;

class SeoRepository extends EntityRepository
{
    public function findByRouteNameQb($route)
    {
        return $this
            ->createQuerybuilder('seo')
            ->where('seo.route LIKE :route')
            ->setParameter('route', sprintf('%%name=%s,', $route));
    }

    public function findByRouteNameAndParameters($route, $parameters)
    {
        $qb = $this->findByRouteNameQb($route);

        $results = $qb->getQuery()->getResult();
        $seos = new ArrayCollection();
        /* @var $seo \KRG\CmsBundle\Entity\SeoInterface */
        foreach ($results as $seo) {
            // "==" TRUE if $a and $b have the same key/value pairs, not matter the order
            if ($seo->getRouteParams() == $parameters) {
                $seos->add($seo);
            }
        }

        return $seos;
    }

    public function findOneBySeoPageKey($key)
    {
        return $this
            ->createQuerybuilder('seo')
            ->join('seo.seoPage', 'seo_page')
            ->where('seo.enabled = 1')
            ->andWhere('seo_page.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findEnabledQb()
    {
        return $this
            ->createQuerybuilder('seo')
            ->where('seo.enabled = 1');
    }

    public function findAllActivesUrls()
    {
        $results = $this
            ->findEnabledQb()
            ->select('seo.url')
            ->getQuery()
            ->getArrayResult();

        return array_column($results, 'url');
    }
}
