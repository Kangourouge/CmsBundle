<?php

namespace KRG\SeoBundle\Entity\Validator;

use Doctrine\ORM\EntityManagerInterface;
use KRG\SeoBundle\Entity\BlockFormInterface;
use KRG\SeoBundle\Entity\BlockInterface;
use KRG\SeoBundle\Form\SeoFormRegistry;
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
     * @var SeoFormRegistry
     */
    protected $registry;

    /**
     * UniqueKey constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, SeoFormRegistry $registry)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
    }

    public function validate($blockForm, Constraint $constraint)
    {
        dump($blockForm);
        die;
        $seoForm = $this->registry->get($blockForm->getFormType());
        $form = $this->formFactory->create($seoForm['form'], null, ['csrf_protection' => false]);

        $request = new Request();
        try {
            $form->submit($blockForm->getPureFormData());
            $form->handleRequest($request);
            if ($seoForm['handler']) {
                $seoForm['handler']->perform($request, $form);
            }
        } catch (\Exception $exception) {
            $this->context->buildViolation($constraint->message)
                ->atPath('form')
                ->setParameter('{{ string }}', $blockForm->getId())
                ->addViolation();
        }
    }
}
