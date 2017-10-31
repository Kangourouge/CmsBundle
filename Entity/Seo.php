<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Seo
 *
 * @ORM\MappedSuperclass(repositoryClass="KRG\SeoBundle\Repository\SeoRepository")
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
     * @ORM\Column(type="boolean", name="is_enabled", options={"default":false})
     */
    protected $enabled;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $uid;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $url;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaTitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaRobots;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ogTitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ogDescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ogImage;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set enabled
     *
     * @param $enabled
     *
     * @return SeoInterface
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set uid
     *
     * @param string $uid
     *
     * @return SeoInterface
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * Get uid
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return SeoInterface
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
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
     * Set metaRobots
     *
     * @param string $metaRobots
     *
     * @return SeoInterface
     */
    public function setMetaRobots($metaRobots)
    {
        $this->metaRobots = $metaRobots;

        return $this;
    }

    /**
     * Get metaRobots
     *
     * @return string
     */
    public function getMetaRobots()
    {
        return $this->metaRobots;
    }

    /**
     * Set ogTitle
     *
     * @param string $ogTitle
     *
     * @return SeoInterface
     */
    public function setOgTitle($ogTitle)
    {
        $this->ogTitle = $ogTitle;

        return $this;
    }

    /**
     * Get ogTitle
     *
     * @return string
     */
    public function getOgTitle()
    {
        return $this->ogTitle;
    }

    /**
     * Set ogDescription
     *
     * @param string $ogDescription
     *
     * @return SeoInterface
     */
    public function setOgDescription($ogDescription)
    {
        $this->ogDescription = $ogDescription;

        return $this;
    }

    /**
     * Get ogDescription
     *
     * @return string
     */
    public function getOgDescription()
    {
        return $this->ogDescription;
    }

    /**
     * Set ogImage
     *
     * @param string $ogImage
     *
     * @return SeoInterface
     */
    public function setOgImage($ogImage)
    {
        $this->ogImage = $ogImage;

        return $this;
    }

    /**
     * Get ogImage
     *
     * @return string
     */
    public function getOgImage()
    {
        return $this->ogImage;
    }

    /**
     * @param array $parameters
     *
     * @return int
     */
    public function diff(array $parameters)
    {
        return count(array_diff_assoc(array_filter($this->getRouteParams()), $parameters));
    }

    /**
     * @param array $parameters
     *
     * @return bool
     */
    public function isValid(array $parameters)
    {
        return $this->diff($parameters) === 0;
    }
}
