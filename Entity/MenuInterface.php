<?php

namespace KRG\CmsBundle\Entity;

use GEGM\CommonBundle\Entity\Tree\NestedTreeInterface;

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
     * Set url
     *
     * @param string $url
     *
     * @return Menu
     */
    public function setUrl($url);

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
     * @return User
     */
    public function setRoles(array $roles);

    /**
     * Set compound
     *
     * @param $compound
     *
     * @return BlockInterface
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
     * Set enabled
     *
     * @param $enabled
     *
     * @return BlockInterface
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
