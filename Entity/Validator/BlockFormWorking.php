<?php

namespace KRG\CmsBundle\Entity\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BlockFormWorking extends Constraint
{
    public $message = 'The block form "{{ string }}" is not working with those parameters';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
