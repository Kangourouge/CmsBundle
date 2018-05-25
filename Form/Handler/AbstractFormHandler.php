<?php

namespace KRG\CmsBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFormHandler implements FormHandlerInterface
{
    public function perform(Request $request, FormInterface $form)
    {
        return null;
    }
}
