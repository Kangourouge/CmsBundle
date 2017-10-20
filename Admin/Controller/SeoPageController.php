<?php

namespace KRG\SeoBundle\Admin\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use KRG\SeoBundle\Entity\SeoPageInterface;
use KRG\SeoBundle\Form\SeoFormRegistry;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class SeoPageController extends BaseAdminController
{
    protected function createSeoPageEntityFormBuilder(SeoPageInterface $entity, $view)
    {
        $formBuilder = $this->createEntityFormBuilder($entity, $view);

        // Assign Seo.form choices
        $formBuilder->remove('formType');
        $formBuilder->add('formType', ChoiceType::class, [
            'choices'  => $this->getAliasChoices(),
            'disabled' => (bool)$entity->getId(), // Disabled on edition
            'required' => false
        ]);

        if ($formBuilder)
        $formBuilder->remove('formData');
        $formBuilder->add('formData', HiddenType::class, [
            'required' => false,
            'data'     => $entity->getFormData() ? json_encode($entity->getFormData()) : $this->request->get('formData'),
        ]);

        return $formBuilder;
    }

    private function getAliasChoices()
    {
        $choices = [];
        foreach ($this->get(SeoFormRegistry::class)->all() as $key => $value) {
            $choices[sprintf('%s (%s)', $value['alias'], $key)] = $key;
        }

        return $choices;
    }
}
