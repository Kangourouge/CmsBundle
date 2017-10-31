<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use KRG\SeoBundle\Entity\Validator\UniqueKey;

/**
 * Block
 *
 * @ORM\MappedSuperclass()
 * @UniqueKey()
 */
class Block extends AbstractBlock implements BlockInterface, BlockContentInterface
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }
}
