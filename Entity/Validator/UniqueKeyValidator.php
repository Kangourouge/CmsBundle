<?php

namespace KRG\CmsBundle\Entity\Validator;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Entity\BlockInterface;
use KRG\CmsBundle\Entity\PageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueKeyValidator extends ConstraintValidator
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Check all blocks if some has the same key
     */
    public function validate($entity, Constraint $constraint)
    {
        if ($entity instanceof BlockInterface) {
            $key = $entity->getKey();

            $filters = $this->entityManager->getRepository(FilterInterface::class)->findBy(['key' => $key]);
            $blocks = $this->entityManager->getRepository(BlockInterface::class)->findBy(['key' => $key]);
            $pages = $this->entityManager->getRepository(PageInterface::class)->findBy(['key' => $key]);

            $filters = $this->filterBlocks($filters, $entity);
            $blocks = $this->filterBlocks($blocks, $entity);
            $pages = $this->filterBlocks($pages, $entity);

            if (count($filters) > 0 || count($blocks) > 0 || count($pages) > 0) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('key')
                    ->setParameter('{{ string }}', $entity->getKey())
                    ->addViolation();
            }
        }
    }

    /**
     * Remove current block
     */
    private function filterBlocks($blocks, $currentBlock)
    {
        return array_filter($blocks, function(BlockInterface $block) use ($currentBlock) {
            return $block !== $currentBlock;
        });
    }
}
