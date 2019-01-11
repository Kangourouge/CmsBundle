<?php
namespace KRG\CmsBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use EMC\FileinputBundle\Entity\FileInterface;
use KRG\CmsBundle\Form\DataTransformer\Base64DataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Base64FileType extends AbstractType
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new Base64DataTransformer($this->entityManager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FileInterface::class,
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'krg_cms_base64_file';
    }
}
