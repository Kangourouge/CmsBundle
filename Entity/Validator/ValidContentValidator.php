<?php

namespace KRG\CmsBundle\Entity\Validator;

use KRG\CmsBundle\Entity\BlockContentInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Twig\Error\Error;
use Twig\Environment;

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
