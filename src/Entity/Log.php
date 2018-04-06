<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogRepository")
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time")
     */
    private $timeEntered;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $timeExit;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $estimatedTime;

    /**
     * @ORM\Column(type="date")
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSuspicious;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Visitor", inversedBy="logs")
     * @ORM\JoinColumn(name="visitor_id", referencedColumnName="id")
     */
    private $visitor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Office", inversedBy="logs")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

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
    public function getTimeEntered()
    {
        return $this->timeEntered;
    }

    /**
     * @param mixed $timeEntered
     */
    public function setTimeEntered($timeEntered): void
    {
        $this->timeEntered = $timeEntered;
    }

    /**
     * @return mixed
     */
    public function getTimeLeft()
    {
        return $this->timeLeft;
    }

    /**
     * @param mixed $timeLeft
     */
    public function setTimeLeft($timeLeft): void
    {
        $this->timeLeft = $timeLeft;
    }

    /**
     * @return mixed
     */
    public function getEstimatedTime()
    {
        return $this->estimatedTime;
    }

    /**
     * @param mixed $estimatedTime
     */
    public function setEstimatedTime($estimatedTime): void
    {
        $this->estimatedTime = $estimatedTime;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return mixed
     */
    public function getisSuspicious()
    {
        return $this->isSuspicious;
    }

    /**
     * @param mixed $isSuspicious
     */
    public function setIsSuspicious($isSuspicious): void
    {
        $this->isSuspicious = $isSuspicious;
    }

    /**
     * @return mixed
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * @param mixed $visitor
     */
    public function setVisitor($visitor): void
    {
        $this->visitor = $visitor;
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

}
