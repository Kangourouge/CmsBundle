<?php

namespace KRG\SeoBundle\Entity;

interface BlockFormInterface extends BlockInterface
{
    /**
     * Set form
     *
     * @param array $form
     *
     * @return BlockFormInterface
     */
    public function setForm(array $form);

    /**
     * Get form
     *
     * @return array
     */
    public function getForm();
}
