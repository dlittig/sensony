<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\Role\Role as SymfonyRole;
/**
 * Role
 */
class Role extends SymfonyRole {

    public function __construct($role)
    {
        parent::__construct($role);
    }

    public function __toString() {
        return $this->getFriendlyName();
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $friendlyName;


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
     * @return Role
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
     * Set friendlyName
     *
     * @param string $friendlyName
     *
     * @return Role
     */
    public function setFriendlyName($friendlyName)
    {
        $this->friendlyName = $friendlyName;

        return $this;
    }

    /**
     * Get friendlyName
     *
     * @return string
     */
    public function getFriendlyName()
    {
        return $this->friendlyName;
    }
}
