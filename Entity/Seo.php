<?php

namespace KRG\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Seo
 *
 * @ORM\MappedSuperclass(repositoryClass="KRG\CmsBundle\Repository\SeoRepository")
 * @Gedmo\Loggable
 */
class Seo implements SeoInterface
{
    use SeoRouteTrait;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Versioned
     */
    protected $uid;

    /**
     * @Assert\NotNull()
     * @Gedmo\Translatable()
     * @Gedmo\Versioned
     * @ORM\Column(type="string", nullable=false)
     */
    protected $url;

    /**
     * @Gedmo\Translatable()
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaTitle;

    /**
     * @Gedmo\Translatable()
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaRobots;

    /**
     * @ORM\Column(type="boolean", name="is_enabled", options={"default":false})
     * @Gedmo\Versioned
     */
    protected $enabled;

    public function __construct()
    {
        $this->enabled = false;
        $this->route = [];
    }

    public function __toString()
    {
        return $this->getUid() ?: '';
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * {@inheritdoc}
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     *
     * @return SeoInterface
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return SeoInterface
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function diff(array $parameters)
    {
        return count(array_diff_assoc(array_filter($this->getRouteParams()), $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(array $parameters)
    {
        return $this->diff($parameters) === 0;
    }
}
