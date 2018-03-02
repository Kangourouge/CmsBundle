<?php

namespace KRG\CmsBundle\Entity\Validator;

use KRG\CmsBundle\Entity\BlockContentInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Twig\Environment;
use Twig\Error\Error;

class ValidContentValidator extends ConstraintValidator
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * ValidContentValidator constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param mixed      $entity
     * @param Constraint $constraint
     * @throws \Twig_Error_Syntax
     */
    public function validate($entity, Constraint $constraint)
    {
        if ($entity instanceof BlockContentInterface) {
            try {
                $nodes = $this->twig->parse($this->twig->tokenize(new \Twig_Source($entity->getContent(), 'base.html.twig'))); // TODO: hmm ?
                $this->twig->compile($nodes);
            } catch (Error $e) {
                $this->context->buildViolation($e->getRawMessage())
                    ->atPath('content')
                    ->addViolation();
            }
        }
    }
}
