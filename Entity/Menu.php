<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use GEGM\CommonBundle\Entity\Tree\NestedTreeEntity;

/**
 * Menu
 *
 * @ORM\MappedSuperclass(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 * @Gedmo\Tree(type="nested")
 */
class Menu implements MenuInterface
{
    use NestedTreeEntity;
    use SeoRouteTrait;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     * @var string
     */
    protected $route;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $url;

    /**
     * @ORM\ManyToOne(targetEntity="KRG\SeoBundle\Entity\MenuInterface", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * @Gedmo\TreeParent
     * @var MenuInterface
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="KRG\SeoBundle\Entity\MenuInterface", mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC"})
     * @var Collection
     */
    protected $children;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $position;

    /**
     * @ORM\Column(type="boolean", name="is_enabled")
     * @var boolean
     */
    protected $enabled;

    public function __construct()
    {
        $this->route = [];
        $this->enabled = false;
        $this->children = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Menu
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set title
     *
     * @param string $title
     *
     * @return Menu
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
     * Set url
     *
     * @param string $url
     *
     * @return Menu
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
     * Set position
     *
     * @param integer $position
     *
     * @return Menu
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set enabled
     *
     * @param $enabled
     *
     * @return BlockInterface
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
}
