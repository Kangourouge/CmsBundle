<?php

namespace KRG\SeoBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use KRG\SeoBundle\Entity\SeoInterface;

/**
 * Class SeoRepository
 * @package KRG\SeoBundle\Repository
 */
class SeoRepository extends EntityRepository
{
    /**
     * @param $route
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByRouteNameQb($route)
    {
        $qb = $this
            ->createQuerybuilder('s')
            ->where('s.route = :route')
            ->setParameter('route', $route);

        return $qb;
    }

    /**
     * @param $route
     * @param $parameters
     * @return ArrayCollection
     */
    public function findByRouteNameAndParameters($route, $parameters)
    {
        $qb = $this->findByRouteNameQb($route);

        $results = $qb->getQuery()->getResult();
        $seos = new ArrayCollection();
        /* @var $seo \KRG\SeoBundle\Entity\SeoInterface */
        foreach ($results as $seo) {
            // "==" TRUE if $a and $b have the same key/value pairs, not matter the order
            if ($seo->getParameters() == $parameters) {
                $seos->add($seo);
            }
        }

        return $seos;
    }

    /**
     * @param $key
     * @return null|SeoInterface
     */
    public function findOneBySeoPageKey($key)
    {
        return $this
            ->createQuerybuilder('s')
            ->join('s.seoPage', 'sp')
            ->where('s.enabled = 1')
            ->andWhere('sp.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
