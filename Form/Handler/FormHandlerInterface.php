<?php

namespace KRG\CmsBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface FormHandlerInterface
{
    /**
     * @param Request $request
     * @param FormInterface $form
     * @return mixed
     */
    public function perform(Request $request, FormInterface $form);
}
