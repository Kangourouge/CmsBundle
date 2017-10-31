<?php

namespace KRG\SeoBundle\Entity\Validator;

use Doctrine\ORM\EntityManagerInterface;
use KRG\SeoBundle\Entity\BlockFormInterface;
use KRG\SeoBundle\Entity\BlockInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueKeyValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * UniqueKey constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Check all blocks if some has the same key
     * @param mixed $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        if ($entity instanceof BlockInterface) {
            $key = $entity->getKey();
            $blockForms = $this->entityManager->getRepository(BlockFormInterface::class)->findBy(['key' => $key]);
            $blocks = $this->entityManager->getRepository(BlockInterface::class)->findBy(['key' => $key]);
            $blockForms = $this->filterBlocks($blockForms, $entity);
            $blocks = $this->filterBlocks($blocks, $entity);

            if (count($blockForms) > 0 || count($blocks) > 0) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('key')
                    ->setParameter('{{ string }}', $entity->getKey())
                    ->addViolation();
            }
        }
    }

    /**
     * Remove current block
     *
     * @param $blocks
     * @param $currentBlock
     * @return array
     */
    private function filterBlocks($blocks, $currentBlock)
    {
        return array_filter($blocks, function(BlockInterface $block) use ($currentBlock) {
            return $block !== $currentBlock;
        });
    }
}
