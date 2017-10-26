<?php

namespace KRG\SeoBundle\Entity;

interface BlockStaticInterface extends BlockInterface
{
    /**
     * Set content
     *
     * @param string $content
     *
     * @return BlockStaticInterface
     */
    public function setContent($content);

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();
}
