<?php

namespace KRG\CmsBundle\Finder;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\FilterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class SeoFinder
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var FilesystemAdapter */
    protected $filesystemAdapter;

    public function __construct(EntityManagerInterface $entityManager, string $dataCacheDir)
    {
        $this->entityManager = $entityManager;
        $this->filesystemAdapter = new FilesystemAdapter('finder', 0, $dataCacheDir);
    }

    /**
     * Find Seo by related filter form data fields
     * Ex: ->findSeoByFilterData(['city' => $address->getCity()], SeoType::class);
     */
    public function findSeoByFilterData(array $data, string $formType = null)
    {
        $key = sha1(json_encode($data).$formType);
        $item = $this->filesystemAdapter->getItem($key);
        if ($item->isHit()) {
            return $item->get();
        }

        $filterRepository = $this->entityManager->getRepository(FilterInterface::class);
        $filters = $filterRepository->findBy(['enabled' => 1, 'working' => 1]);

        $seo = null;
        $nbParamsFound = 0;
        $nbParamsSearched = count($data);
        foreach ($filters as $filter) {
            $form = $filter->getForm();

            if ($formType && $form['type'] !== $formType) {
                continue;
            }

            foreach ($form['data'] as $field => $value) {
                foreach ($data as $fieldSearched => $valueSearched) {
                    if (strstr($field, $fieldSearched) && strtolower($value) === strtolower($valueSearched)) {
                        $nbParamsFound++;

                        if ($nbParamsFound === $nbParamsSearched) {
                            $item->set($filter->getSeo());
                            $this->filesystemAdapter->save($item);

                            return $filter->getSeo();
                        }
                    }
                }
            }
        }

        return null;
    }
}
