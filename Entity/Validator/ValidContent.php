<?php

namespace KRG\CmsBundle\Entity\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidContent extends Constraint
{
    public $message = 'The block content is not working';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
