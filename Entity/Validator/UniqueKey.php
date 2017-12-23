<?php

namespace KRG\CmsBundle\Entity\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueKey extends Constraint
{
    public $message = 'The block key "{{ string }}" is already used';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
