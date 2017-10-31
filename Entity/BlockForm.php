<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlockForm
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="krg_block_form")
 */
class BlockForm extends AbstractBlock implements BlockFormInterface
{
    /**
     * @ORM\Column(type="json_array")
     * @var array
     */
    protected $form;

    /**
     * Set form
     *
     * @param array $form
     *
     * @return BlockFormInterface
     */
    public function setForm(array $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return array
     */
    public function getForm()
    {
        return $this->form;
    }
}
