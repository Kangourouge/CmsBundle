<?php

namespace KRG\SeoBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface FormHandlerInterface
{
    /**
     * @param Request $request
     * @param FormInterface $form
     * @return mixed
     */
    public function handle(Request $request, FormInterface $form);
}
