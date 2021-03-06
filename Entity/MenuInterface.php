<?php

namespace KRG\CmsBundle\Entity;

use KRG\DoctrineExtensionBundle\Entity\Tree\NestedTreeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface MenuInterface extends NestedTreeInterface, SeoRouteInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Menu
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set key
     *
     * @param string $key
     *
     * @return MenuInterface
     */
    public function setKey($key);

    /**
     * Get key
     *
     * @return string
     */
    public function getKey();

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Menu
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();


    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon();

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return MenuInterface
     */
    public function setIcon($icon);

    /**
     * Set content
     *
     * @param string $content
     *
     * @return MenuInterface
     */
    public function setContent($content);

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Menu
     */
    public function setPosition($position);

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition();

    /**
     * Add role
     *
     * @param $role
     * @return $this
     */
    public function addRole($role);

    /**
     * Remove role
     *
     * @param $role
     * @return $this
     */
    public function removeRole($role);

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles();

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return UserInterface
     */
    public function setRoles(array $roles);

    /**
     * Set compound
     *
     * @param $compound
     *
     * @return MenuInterface
     */
    public function setCompound($compound);

    /**
     * Get compound
     *
     * @return boolean
     */
    public function getCompound();

    /**
     * Is compound
     *
     * @return boolean
     */
    public function isCompound();

    /**
     * Set breadcrumbDisplay
     *
     * @param $breadcrumbDisplay
     *
     * @return MenuInterface
     */
    public function setBreadcrumbDisplay($breadcrumbDisplay);

    /**
     * Get breadcrumbDisplay
     *
     * @return boolean
     */
    public function getBreadcrumbDisplay();

    /**
     * Is breadcrumbDisplay
     *
     * @return boolean
     */
    public function isBreadcrumbDisplay();

    /**
     * Set enabled
     *
     * @param $enabled
     *
     * @return MenuInterface
     */
    public function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled();

    /**
     * Is enabled
     *
     * @return boolean
     */
    public function isEnabled();
}
