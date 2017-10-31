<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use KRG\SeoBundle\Entity\Validator\BlockFormWorking;
use KRG\SeoBundle\Entity\Validator\UniqueKey;

/**
 * BlockForm
 *
 * @ORM\MappedSuperclass
 * @BlockFormWorking()
 * @UniqueKey()
 */
class BlockForm extends AbstractBlock implements BlockFormInterface
{
    /**
     * @ORM\Column(type="json_array")
     * @var array
     */
    protected $form;

    /**
     * {@inheritdoc}
     */
    public function getFormType()
    {
        return $this->form['type'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormData()
    {
        return $this->form['data'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPureFormData()
    {
        $pureData = [];
        foreach ($this->getFormData() as $name => $data) {
            if (preg_match('/\[([^\]]*)\]/', $name, $match)) {
                $pureData[$match[1]] = $data;
            }
        }

        return $pureData;
    }

    /**
     * {@inheritdoc}
     */
    public function setForm(array $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return $this->form;
    }
}
