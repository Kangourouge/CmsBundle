<?php

namespace KRG\CmsBundle\Form\Extension;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use KRG\CmsBundle\Form\DataTransformer\CKEditorDataTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class CKEditorTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CKEditorDataTransformer());
    }

    public function getExtendedType()
    {
        return CKEditorType::class;
    }
}
