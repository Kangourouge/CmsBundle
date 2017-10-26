<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlockStatic
 *
 * @ORM\MappedSuperclass()
 * @ORM\Table(name="krg_block_static")
 */
class BlockStatic extends AbstractBlock implements BlockStaticInterface
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
     * @return BlockStaticInterface
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
