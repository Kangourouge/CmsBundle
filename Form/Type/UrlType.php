<?php

namespace KRG\CmsBundle\Form\Type;

use KRG\CmsBundle\Routing\UrlResolver;
use KRG\CmsBundle\Form\DataTransformer\UrlDataTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class UrlType extends TextType
{
    /** @var UrlResolver */
    protected $urlResolver;

    /**
     * UrlDataTransformer constructor.
     *
     * @param UrlResolver $urlResolver
     */
    public function __construct(UrlResolver $urlResolver)
    {
        $this->urlResolver = $urlResolver;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new UrlDataTransformer($this->urlResolver));
    }
}
