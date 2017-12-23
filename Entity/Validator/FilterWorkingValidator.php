<?php

namespace KRG\CmsBundle\Entity\Validator;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Entity\BlockInterface;
use KRG\CmsBundle\Form\FilterRegistry;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FilterWorkingValidator extends ConstraintValidator
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
     * @var FilterRegistry
     */
    protected $registry;

    /**
     * UniqueKey constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, FilterRegistry $registry)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
    }

    public function validate($filter, Constraint $constraint)
    {

        $config = $this->registry->get($filter->getFormType());
        $form = $this->formFactory->create($config['form'], null, ['csrf_protection' => false]);

        $request = new Request();
        try {
            $form->submit($filter->getPureFormData());
            $form->handleRequest($request);
            if ($config['handler']) {
                $config['handler']->perform($request, $form);
            }
        } catch (\Exception $exception) {
            $this->context->buildViolation($constraint->message)
                ->atPath('form')
                ->setParameter('{{ string }}', $filter->getId())
                ->addViolation();
        }
    }
}
