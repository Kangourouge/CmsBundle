<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Seo
 *
 * @ORM\MappedSuperclass(repositoryClass="KRG\SeoBundle\Repository\SeoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Seo implements SeoInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    protected $enabled;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $uid;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $route;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $parameters;

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
        $this->routeParameters = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getUid() ?: '';
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist()
    {
        $route = $this->route;

        $prefix = preg_match("/^krg_seo_.+/", $route) ? '' : 'krg_seo_';
        $this->uid = sprintf('%s%s_%s', $prefix, $this->route, uniqid());

        $this->slugifyUrl();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate()
    {
        $this->slugifyUrl();
    }

    // Todo: suglify URL
    public function slugifyUrl()
    {
        if ($this->url && $this->url[0] != '/') {
            $this->url = '/'.$this->url;
        }

        // $slugify = new Slugify();
        // $this->url = $slugify->slugify($this->url));
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
     * Set route
     *
     * @param string $route
     *
     * @return SeoInterface
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
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
     * Set parameters
     *
     * @param array $parameters
     *
     * @return SeoInterface
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getRouteParameters()
    {
        if ($this->seoPage === null || $this->parameters === null) {
            return $this->parameters;
        }

        return array_merge($this->parameters, array('id' => $this->seoPage->getId()));
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

    public function diff(array $parameters)
    {
        return count(array_diff_assoc(array_filter($this->parameters), $parameters));
    }

    public function isValid(array $parameters)
    {
        return $this->diff($parameters) === 0;
    }
}
