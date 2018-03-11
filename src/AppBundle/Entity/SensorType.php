<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class SensorType {

    public function __toString() {
        return $this->name;
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
    private $description;

    /**
     * @var string
     */
    private $mapping;


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
     * @return SensorType
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
     * Set description
     *
     * @param string $description
     *
     * @return SensorType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set mapping
     *
     * @param string $mapping
     *
     * @return SensorType
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;

        return $this;
    }

    /**
     * Get mapping
     *
     * @return string
     */
    public function getMapping()
    {
        return $this->mapping;
    }
}
