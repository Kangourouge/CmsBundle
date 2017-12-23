<?php

namespace KRG\CmsBundle\Entity\Validator;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\BlockFormInterface;
use KRG\CmsBundle\Entity\BlockInterface;
use KRG\CmsBundle\Form\BlockFormRegistry;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BlockFormWorkingValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var BlockFormRegistry
     */
    protected $registry;

    /**
     * UniqueKey constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, BlockFormRegistry $registry)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
    }

    public function validate($blockForm, Constraint $constraint)
    {

        $config = $this->registry->get($blockForm->getFormType());
        $form = $this->formFactory->create($config['form'], null, ['csrf_protection' => false]);

        $request = new Request();
        try {
            $form->submit($blockForm->getPureFormData());
            $form->handleRequest($request);
            if ($config['handler']) {
                $config['handler']->perform($request, $form);
            }
        } catch (\Exception $exception) {
            $this->context->buildViolation($constraint->message)
                ->atPath('form')
                ->setParameter('{{ string }}', $blockForm->getId())
                ->addViolation();
        }
    }
}
