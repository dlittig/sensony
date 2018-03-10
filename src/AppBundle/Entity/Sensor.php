<?php

namespace AppBundle\Entity;

use AppBundle\Globals\Utils;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SensorRepository")
 */
class Sensor {

    public function __construct() {
        $this->token = Utils::generateRandomString(64);
    }

    public function __toString(): string {
        return $this->name . ' - ' . $this->getSensorType();
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $data;

    /**
     * @var \AppBundle\Entity\SensorType
     */
    private $sensorType;


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
     * Set latitude
     *
     * @param float $latitude
     *
     * @return Sensor
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return Sensor
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return Sensor
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Sensor
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
     * @return Sensor
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
     * Add datum
     *
     * @param \AppBundle\Entity\Data $datum
     *
     * @return Sensor
     */
    public function addDatum(\AppBundle\Entity\Data $datum)
    {
        $this->data[] = $datum;

        return $this;
    }

    /**
     * Remove datum
     *
     * @param \AppBundle\Entity\Data $datum
     */
    public function removeDatum(\AppBundle\Entity\Data $datum)
    {
        $this->data->removeElement($datum);
    }

    /**
     * Get data
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set sensorType
     *
     * @param \AppBundle\Entity\SensorType $sensorType
     *
     * @return Sensor
     */
    public function setSensorType(\AppBundle\Entity\SensorType $sensorType = null)
    {
        $this->sensorType = $sensorType;

        return $this;
    }

    /**
     * Get sensorType
     *
     * @return \AppBundle\Entity\SensorType
     */
    public function getSensorType()
    {
        return $this->sensorType;
    }
}
