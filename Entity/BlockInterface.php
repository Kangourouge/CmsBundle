<?php

namespace KRG\SeoBundle\Entity;

interface BlockInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set enabled
     *
     * @param $enabled
     *
     * @return BlockInterface
     */
    public function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled();

    /**
     * Set key
     *
     * @param string $key
     *
     * @return BlockInterface
     */
    public function setKey($key);

    /**
     * Get key
     *
     * @return string
     */
    public function getKey();
}
