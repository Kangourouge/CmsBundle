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
     * CKEditorTypeExtension constructor.
     * @param $webDir
     * @param $ckUploadDir
     */
    public function __construct($webDir, $ckUploadDir)
    {
        $this->webDir = $webDir;
        $this->uploadDir = $ckUploadDir;
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
