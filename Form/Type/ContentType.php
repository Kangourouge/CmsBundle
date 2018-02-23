<?php

namespace KRG\CmsBundle\Form\Type;

use KRG\CmsBundle\Form\DataTransformer\ContentTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Templating\EngineInterface;

class ContentType extends AbstractType
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addModelTransformer(new ContentTransformer($this->templating));
    }

    public function getParent()
    {
        return TextareaType::class;
    }
}
