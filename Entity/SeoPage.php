<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SeoPage
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
abstract class SeoPage implements SeoPageInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Seo", inversedBy="seoPage", cascade={"all"})
     * @ORM\JoinColumn(name="seo_id", referencedColumnName="id")
     */
    protected $seo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $formRoute;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $formParameters;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $formType;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $formData;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $preContent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $postContent;

    /**
     * @var string
     */
    private $className;

    public function __construct()
    {
        $this->className = static::class;
    }

    function __toString()
    {
        return $this->getSeo() ? $this->getSeo()->getUrl() : '';
    }

    /* */

    /**
     * @ORM\PostLoad()
     */
    public function onPostLoad()
    {
        $this->className = static::class;
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
     * Set seo
     *
     * @param SeoInterface $seo
     *
     * @return SeoPageInterface
     */
    public function setSeo(SeoInterface $seo = null)
    {
        $this->seo = $seo;

        return $this;
    }

    /**
     * Get seo
     *
     * @return SeoInterface
     */
    public function getSeo()
    {
        return $this->seo;
    }

    /**
     * Set formRoute
     *
     * @param string $formRoute
     *
     * @return SeoPage
     */
    public function setFormRoute($formRoute)
    {
        $this->formRoute = $formRoute;

        return $this;
    }

    /**
     * Get formRoute
     *
     * @return string
     */
    public function getFormRoute()
    {
        return $this->formRoute;
    }

    /**
     * Set formType
     *
     * @param string $formType
     *
     * @return SeoPage
     */
    public function setFormType($formType)
    {
        $this->formType = $formType;

        return $this;
    }

    /**
     * Get formType
     *
     * @return string
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * Set formParameters
     *
     * @param array $formParameters
     *
     * @return SeoPage
     */
    public function setFormParameters($formParameters)
    {
        $this->formParameters = $formParameters;

        return $this;
    }

    /**
     * Get formParameters
     *
     * @return array
     */
    public function getFormParameters()
    {
        return $this->formParameters;
    }

    /**
     * Set formData
     *
     * @param string $formData
     *
     * @return SeoPage
     */
    public function setFormData($formData)
    {
        unset($formData['_token']);

        $this->formData = $formData;

        return $this;
    }

    /**
     * Get formData
     *
     * @return string
     */
    public function getFormData()
    {
        return $this->formData;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return SeoPage
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set preContent
     *
     * @param string $preContent
     *
     * @return SeoPage
     */
    public function setPreContent($preContent)
    {
        $this->preContent = $preContent;

        return $this;
    }

    /**
     * Get preContent
     *
     * @return string
     */
    public function getPreContent()
    {
        return $this->preContent;
    }

    /**
     * Set postContent
     *
     * @param string $postContent
     *
     * @return SeoPage
     */
    public function setPostContent($postContent)
    {
        $this->postContent = $postContent;

        return $this;
    }

    /**
     * Get postContent
     *
     * @return string
     */
    public function getPostContent()
    {
        return $this->postContent;
    }
}
