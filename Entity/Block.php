<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Block
 *
 * @ORM\MappedSuperclass()
 * @ORM\Table(name="krg_block_static")
 */
class Block extends AbstractBlock implements BlockInterface, BlockContentInterface
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * Set content
     *
     * @param string $content
     *
     * @return BlockInterface
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
