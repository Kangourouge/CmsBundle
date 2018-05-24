<?php

namespace KRG\CmsBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FilterRepository extends EntityRepository
{
    public function findWithSeo()
    {
        return $this
            ->createQuerybuilder('f')
            ->where('f.seo IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
}
