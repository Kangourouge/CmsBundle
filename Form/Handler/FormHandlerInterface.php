<?php

namespace KRG\CmsBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface FormHandlerInterface
{
    public function perform(Request $request, FormInterface $form, array $options = []);
}
