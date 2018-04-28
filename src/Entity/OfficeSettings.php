<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OfficeSettingsRepository")
 */
class OfficeSettings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @ORM\Column(type="integer")
     */
    private $averageWaitingTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $walkTime;

    public function __construct()
    {
        $this->averageWaitingTime = 20;
        $this->walkTime = 5;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param mixed $office
     */
    public function setOffice($office): void
    {
        $this->office = $office;
    }

    /**
     * @return mixed
     */
    public function getAverageWaitingTime()
    {
        return $this->averageWaitingTime;
    }

    /**
     * @param mixed $averageWaitingTime
     */
    public function setAverageWaitingTime($averageWaitingTime): void
    {
        $this->averageWaitingTime = $averageWaitingTime;
    }

    /**
     * @return mixed
     */
    public function getWalkTime()
    {
        return $this->walkTime;
    }

    /**
     * @param mixed $walkTime
     */
    public function setWalkTime($walkTime): void
    {
        $this->walkTime = $walkTime;
    }

}
