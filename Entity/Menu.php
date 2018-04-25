<?php

namespace KRG\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use KRG\DoctrineExtensionBundle\Entity\Sortable\SortableEntity;
use KRG\DoctrineExtensionBundle\Entity\Sortable\SortableInterface;

/**
 * Menu
 *
 * @ORM\MappedSuperclass(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 * @Gedmo\Tree(type="nested")
 * @Gedmo\Loggable
 */
class Menu implements MenuInterface, SortableInterface
{
    use NestedTreeEntity;
    use SortableEntity;
    use SeoRouteTrait;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Gedmo\Versioned
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(name="`key`", type="string", unique=true, nullable=true)
     * @Gedmo\Versioned
     * @var string
     */
    protected $key;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Versioned
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     * @Gedmo\Versioned
     * @var string
     */
    protected $route;

    /**
     * @ORM\ManyToOne(targetEntity="KRG\CmsBundle\Entity\MenuInterface", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * @Gedmo\TreeParent
     * @Gedmo\Versioned
     * @var MenuInterface
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="KRG\CmsBundle\Entity\MenuInterface", mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC"})
     * @var Collection
     */
    protected $children;

    /**
     * @ORM\Column(type="json_array")
     * @Gedmo\Versioned
     * @var string
     */
    protected $roles;

    /**
     * @ORM\Column(type="boolean", name="is_compound", nullable=true)
     * @Gedmo\Versioned
     * @var boolean
     */
    protected $compound;

    /**
     * @ORM\Column(type="boolean", name="is_enabled")
     * @Gedmo\Versioned
     * @var boolean
     */
    protected $enabled;

    public function __construct()
    {
        $this->route = [];
        $this->enabled = false;
        $this->children = new ArrayCollection();
        $this->position = 0;
        $this->roles = [];
    }

    public function __toString()
    {
        return (string) $this->getName();
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->route['url'] ?? null;
    }

    /**
     * Add role
     *
     * @param $role
     * @return $this
     */
    public function addRole($role)
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Remove role
     *
     * @param $role
     * @return $this
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return MenuInterface
     */
    public function setRoles(array $roles)
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCompound($compound)
    {
        $this->compound = $compound;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompound()
    {
        return $this->compound;
    }

    /**
     * {@inheritdoc}
     */
    public function isCompound()
    {
        return $this->compound;
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
}
