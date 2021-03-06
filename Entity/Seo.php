<?php

namespace KRG\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Routing\CompiledRoute;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Seo
 *
 * @ORM\MappedSuperclass(repositoryClass="KRG\CmsBundle\Repository\SeoRepository")
 * @Gedmo\Loggable
 */
class Seo implements SeoInterface, Translatable
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaRobots;

    /**
     * @Gedmo\Translatable()
     * @ORM\Column(type="html", nullable=true)
     */
    protected $preContent;

    /**
     * @Gedmo\Translatable()
     * @ORM\Column(type="html", nullable=true)
     */
    protected $postContent;

    /**
     * @ORM\Column(type="boolean", name="no_index", options={"default": false})
     */
    protected $noIndex;

    /**
     * @ORM\Column(type="boolean", name="no_follow", options={"default": false})
     */
    protected $noFollow;

    /**
     * @ORM\Column(type="boolean", name="is_enabled", options={"default":false})
     * @Gedmo\Versioned
     */
    protected $enabled;

    /**
     * @var CompiledRoute
     */
    protected $compiledRoute;

    public function __construct()
    {
        $this->enabled = false;
        $this->noIndex = false;
        $this->noFollow = false;
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
    public function getNoIndex()
    {
        return $this->noIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function setNoIndex($noIndex)
    {
        $this->noIndex = $noIndex;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isNoIndex()
    {
        return $this->noIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function getNoFollow()
    {
        return $this->noFollow;
    }

    /**
     * {@inheritdoc}
     */
    public function setNoFollow($noFollow)
    {
        $this->noFollow = $noFollow;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isNoFollow()
    {
        return $this->noFollow;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMetaRobots()
    {
        return strlen($this->getMetaRobots()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaRobots()
    {
        if ($this->isNoIndex() && $this->isNoFollow()) {
            return 'noindex, nofollow';
        }
        if ($this->isNoIndex()) {
            return 'noindex';
        }
        if ($this->isNoFollow()) {
            return 'nofollow';
        }

        return '';
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
        if (strlen($url) > 0 && $url[0] !== '/') {
            $url = '/'.$url;
        }

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
     * {@inheritdoc}
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreContent()
    {
        return $this->preContent;
    }

    /**
     * {@inheritdoc}
     */
    public function setPreContent($preContent)
    {
        $this->preContent = $preContent;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostContent()
    {
        return $this->postContent;
    }

    /**
     * {@inheritdoc}
     */
    public function setPostContent($postContent)
    {
        $this->postContent = $postContent;
    }

    /**
     * {@inheritdoc}
     */
    public function diff(array $parameters)
    {
        $_parameters = $this->getRouteParams();
        foreach ($parameters as $key => $value) {
            // Parameter value does not match
            if (isset($_parameters[$key]) && $_parameters[$key] !== $value) {
                return -1;
            }
        }

        return count(array_diff_assoc($_parameters, $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(array $parameters)
    {
        return $this->diff($parameters) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompiledRoute()
    {
        return $this->compiledRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function setCompiledRoute(CompiledRoute $compiledRoute)
    {
        $this->compiledRoute = $compiledRoute;
    }
}
