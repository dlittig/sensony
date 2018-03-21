<?php

namespace AppBundle\Entity;

use AppBundle\Globals\Utils;
use Symfony\Component\Security\Core\User\UserInterface;

class Sensor implements UserInterface {

    public function __construct() {
        $this->token = Utils::generateRandomString(64);
    }

    public function __toString() {
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
    /**
     * @var string
     */
    private $token;


    /**
     * Set token
     *
     * @param string $token
     *
     * @return Sensor
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles() {
        return ["ROLE_SENSOR"];
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword() {
        return $this->token;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt() { return null; }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername() {
        return $this->uuid;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() { }
}
