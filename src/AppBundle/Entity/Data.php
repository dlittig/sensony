<?php

namespace AppBundle\Entity;

/**
 * Data
 */
class Data
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $time;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var float
     */
    private $elevation;

    /**
     * @var float
     */
    private $speed;

    /**
     * @var float
     */
    private $temp;

    /**
     * @var float
     */
    private $moist;

    /**
     * @var float
     */
    private $pressure;

    /**
     * @var float
     */
    private $ADC0;

    /**
     * @var float
     */
    private $ADC1;

    /**
     * @var float
     */
    private $ADC2;

    /**
     * @var float
     */
    private $ADC3;

    /**
     * @var float
     */
    private $ADC4;

    /**
     * @var float
     */
    private $ADC5;

    /**
     * @var float
     */
    private $ADC6;

    /**
     * @var float
     */
    private $ADC7;

    /**
     * @var \AppBundle\Entity\Sensor
     */
    private $sensor;


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
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Data
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Data
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     *
     * @return Data
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
     * @return Data
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
     * Set elevation
     *
     * @param float $elevation
     *
     * @return Data
     */
    public function setElevation($elevation)
    {
        $this->elevation = $elevation;

        return $this;
    }

    /**
     * Get elevation
     *
     * @return float
     */
    public function getElevation()
    {
        return $this->elevation;
    }

    /**
     * Set speed
     *
     * @param float $speed
     *
     * @return Data
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;

        return $this;
    }

    /**
     * Get speed
     *
     * @return float
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Set temp
     *
     * @param float $temp
     *
     * @return Data
     */
    public function setTemp($temp)
    {
        $this->temp = $temp;

        return $this;
    }

    /**
     * Get temp
     *
     * @return float
     */
    public function getTemp()
    {
        return $this->temp;
    }

    /**
     * Set moist
     *
     * @param float $moist
     *
     * @return Data
     */
    public function setMoist($moist)
    {
        $this->moist = $moist;

        return $this;
    }

    /**
     * Get moist
     *
     * @return float
     */
    public function getMoist()
    {
        return $this->moist;
    }

    /**
     * Set pressure
     *
     * @param float $pressure
     *
     * @return Data
     */
    public function setPressure($pressure)
    {
        $this->pressure = $pressure;

        return $this;
    }

    /**
     * Get pressure
     *
     * @return float
     */
    public function getPressure()
    {
        return $this->pressure;
    }

    /**
     * Set aDC0
     *
     * @param float $aDC0
     *
     * @return Data
     */
    public function setADC0($aDC0)
    {
        $this->ADC0 = $aDC0;

        return $this;
    }

    /**
     * Get aDC0
     *
     * @return float
     */
    public function getADC0()
    {
        return $this->ADC0;
    }

    /**
     * Set aDC1
     *
     * @param float $aDC1
     *
     * @return Data
     */
    public function setADC1($aDC1)
    {
        $this->ADC1 = $aDC1;

        return $this;
    }

    /**
     * Get aDC1
     *
     * @return float
     */
    public function getADC1()
    {
        return $this->ADC1;
    }

    /**
     * Set aDC2
     *
     * @param float $aDC2
     *
     * @return Data
     */
    public function setADC2($aDC2)
    {
        $this->ADC2 = $aDC2;

        return $this;
    }

    /**
     * Get aDC2
     *
     * @return float
     */
    public function getADC2()
    {
        return $this->ADC2;
    }

    /**
     * Set aDC3
     *
     * @param float $aDC3
     *
     * @return Data
     */
    public function setADC3($aDC3)
    {
        $this->ADC3 = $aDC3;

        return $this;
    }

    /**
     * Get aDC3
     *
     * @return float
     */
    public function getADC3()
    {
        return $this->ADC3;
    }

    /**
     * Set aDC4
     *
     * @param float $aDC4
     *
     * @return Data
     */
    public function setADC4($aDC4)
    {
        $this->ADC4 = $aDC4;

        return $this;
    }

    /**
     * Get aDC4
     *
     * @return float
     */
    public function getADC4()
    {
        return $this->ADC4;
    }

    /**
     * Set aDC5
     *
     * @param float $aDC5
     *
     * @return Data
     */
    public function setADC5($aDC5)
    {
        $this->ADC5 = $aDC5;

        return $this;
    }

    /**
     * Get aDC5
     *
     * @return float
     */
    public function getADC5()
    {
        return $this->ADC5;
    }

    /**
     * Set aDC6
     *
     * @param float $aDC6
     *
     * @return Data
     */
    public function setADC6($aDC6)
    {
        $this->ADC6 = $aDC6;

        return $this;
    }

    /**
     * Get aDC6
     *
     * @return float
     */
    public function getADC6()
    {
        return $this->ADC6;
    }

    /**
     * Set aDC7
     *
     * @param float $aDC7
     *
     * @return Data
     */
    public function setADC7($aDC7)
    {
        $this->ADC7 = $aDC7;

        return $this;
    }

    /**
     * Get aDC7
     *
     * @return float
     */
    public function getADC7()
    {
        return $this->ADC7;
    }

    /**
     * Set sensor
     *
     * @param \AppBundle\Entity\Sensor $sensor
     *
     * @return Data
     */
    public function setSensor(\AppBundle\Entity\Sensor $sensor = null)
    {
        $this->sensor = $sensor;

        return $this;
    }

    /**
     * Get sensor
     *
     * @return \AppBundle\Entity\Sensor
     */
    public function getSensor()
    {
        return $this->sensor;
    }
}
