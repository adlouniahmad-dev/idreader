<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AppointmentRepository")
 */
class Appointment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $applicantName;

    /**
     * @ORM\Column(type="string")
     */
    private $applicantSsn;

    /**
     * @ORM\Column(type="string")
     */
    private $premiseOwner;

    /**
     * @ORM\Column(type="string")
     */
    private $office;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $time;

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
    public function getApplicantName()
    {
        return $this->applicantName;
    }

    /**
     * @param mixed $applicantName
     */
    public function setApplicantName($applicantName): void
    {
        $this->applicantName = $applicantName;
    }

    /**
     * @return mixed
     */
    public function getApplicantSsn()
    {
        return $this->applicantSsn;
    }

    /**
     * @param mixed $applicantSsn
     */
    public function setApplicantSsn($applicantSsn): void
    {
        $this->applicantSsn = $applicantSsn;
    }

    /**
     * @return mixed
     */
    public function getPremiseOwner()
    {
        return $this->premiseOwner;
    }

    /**
     * @param mixed $premiseOwner
     */
    public function setPremiseOwner($premiseOwner): void
    {
        $this->premiseOwner = $premiseOwner;
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
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time): void
    {
        $this->time = $time;
    }

}
