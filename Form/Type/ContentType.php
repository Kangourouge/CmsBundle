<?php

namespace KRG\CmsBundle\Form\Type;

use KRG\CmsBundle\Form\DataTransformer\ContentTransformer;
use KRG\CmsBundle\Routing\UrlResolver;
use KRG\CmsBundle\Image\FileBase64Uploader;
use Symfony\Component\Form\AbstractType;
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

    /** @var UrlResolver */
    protected $urlResolver;

    /** @var array */
    protected $pageConfig;

    public function __construct(EngineInterface $templating, FileBase64Uploader $fileUploader, UrlResolver $urlResolver, array $pageConfig = [])
    {
        $this->templating = $templating;
        $this->fileUploader = $fileUploader;
        $this->urlResolver = $urlResolver;
        $this->pageConfig = $pageConfig;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ContentTransformer($this->templating, $this->fileUploader, $this->urlResolver));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['responsive'] = $options['responsive'];
        $view->vars['height'] = $options['height'];
        $view->vars['extra_hide_elements'] = $this->pageConfig['extra_hide_elements'] ?? [];
        $view->vars['fragment'] = $options['fragment'];
        $view->vars['attr']['class'] = 'hidden';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'responsive' => [
                    ['label' => 'Desktop', 'width' => '100%'],
                    ['label' => 'Tablet', 'width' => '1024px', 'height' => '1366px'],
                    ['label' => 'Mobile', 'width' => '375px', 'height' => '667px'],
                ],
                'fragment'   => true,
                'height'     => 500,
            ]);
    }

    public function getParent()
    {
        return TextareaType::class;
    }
}
