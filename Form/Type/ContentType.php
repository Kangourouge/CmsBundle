<?php

namespace KRG\CmsBundle\Form\Type;

use KRG\CmsBundle\Form\DataTransformer\ContentTransformer;
use KRG\CmsBundle\Service\FileBase64Uploader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;

class ContentType extends AbstractType
{
    /** @var EngineInterface */
    protected $templating;

    /** @var FileBase64Uploader */
    protected $fileUploader;

    public function __construct(EngineInterface $templating, FileBase64Uploader $fileUploader)
    {
        $this->templating = $templating;
        $this->fileUploader = $fileUploader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addModelTransformer(new ContentTransformer($this->templating, $this->fileUploader));
    }

    public function getParent()
    {
        return HiddenType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['responsive'] = $options['responsive'];
        $view->vars['fragment'] = $options['fragment'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'responsive' => [
                    ['label' => 'Destkop', 'width' => '100%'],
                    ['label' => 'Tablet', 'width' => '1024px', 'height' => '1366px'],
                    ['label' => 'Mobile', 'width' => '375px', 'height' => '667px'],
                ],
                'fragment'   => true,
            ]);
    }
}
