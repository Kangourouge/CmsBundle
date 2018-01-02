<?php

namespace KRG\CmsBundle\Form\Extension;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use KRG\CmsBundle\Form\DataTransformer\CKEditorDataTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class CKEditorTypeExtension extends AbstractTypeExtension
{
    /**
     * @var string
     */
    private $webDir;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * CKEditorDataTransformer constructor.
     * @param $uploadDir
     */
    public function __construct($webDir, $uploadDir)
    {
        $this->webDir = $webDir;
        $this->uploadDir = $uploadDir;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CKEditorDataTransformer($this->webDir, $this->uploadDir));
    }


    public function getExtendedType()
    {
        return CKEditorType::class;
    }
}
