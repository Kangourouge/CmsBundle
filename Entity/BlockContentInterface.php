<?php

namespace KRG\SeoBundle\Entity;

interface BlockContentInterface extends BlockInterface
{
    /**
     * Set content
     *
     * @param string $content
     *
     * @return BlockInterface
     */
    public function setContent($content);

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();
}
