<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogsHistoryRepository")
 */
class LogsHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $logId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $visitorName;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $timeEntered;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $timeLeftFromOffice;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $timeExit;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateEntered;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $building;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $officeName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $guardCheckIn;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $guardCheckOut;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $gateCheckIn;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $gateCheckOut;

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
    public function getLogId()
    {
        return $this->logId;
    }

    /**
     * @param mixed $logId
     */
    public function setLogId($logId): void
    {
        $this->logId = $logId;
    }

    /**
     * @return mixed
     */
    public function getVisitorName()
    {
        return $this->visitorName;
    }

    /**
     * @param mixed $visitorName
     */
    public function setVisitorName($visitorName): void
    {
        $this->visitorName = $visitorName;
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
    public function getTimeLeftFromOffice()
    {
        return $this->timeLeftFromOffice;
    }

    /**
     * @param mixed $timeLeftFromOffice
     */
    public function setTimeLeftFromOffice($timeLeftFromOffice): void
    {
        $this->timeLeftFromOffice = $timeLeftFromOffice;
    }

    /**
     * @return mixed
     */
    public function getTimeExit()
    {
        return $this->timeExit;
    }

    /**
     * @param mixed $timeExit
     */
    public function setTimeExit($timeExit): void
    {
        $this->timeExit = $timeExit;
    }

    /**
     * @return mixed
     */
    public function getDateEntered()
    {
        return $this->dateEntered;
    }

    /**
     * @param mixed $dateEntered
     */
    public function setDateEntered($dateEntered): void
    {
        $this->dateEntered = $dateEntered;
    }

    /**
     * @return mixed
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param mixed $building
     */
    public function setBuilding($building): void
    {
        $this->building = $building;
    }

    /**
     * @return mixed
     */
    public function getOfficeName()
    {
        return $this->officeName;
    }

    /**
     * @param mixed $officeName
     */
    public function setOfficeName($officeName): void
    {
        $this->officeName = $officeName;
    }

    /**
     * @return mixed
     */
    public function getGuardCheckIn()
    {
        return $this->guardCheckIn;
    }

    /**
     * @param mixed $guardCheckIn
     */
    public function setGuardCheckIn($guardCheckIn): void
    {
        $this->guardCheckIn = $guardCheckIn;
    }

    /**
     * @return mixed
     */
    public function getGuardCheckOut()
    {
        return $this->guardCheckOut;
    }

    /**
     * @param mixed $guardCheckOut
     */
    public function setGuardCheckOut($guardCheckOut): void
    {
        $this->guardCheckOut = $guardCheckOut;
    }

    /**
     * @return mixed
     */
    public function getGateCheckIn()
    {
        return $this->gateCheckIn;
    }

    /**
     * @param mixed $gateCheckIn
     */
    public function setGateCheckIn($gateCheckIn): void
    {
        $this->gateCheckIn = $gateCheckIn;
    }

    /**
     * @return mixed
     */
    public function getGateCheckOut()
    {
        return $this->gateCheckOut;
    }

    /**
     * @param mixed $gateCheckOut
     */
    public function setGateCheckOut($gateCheckOut): void
    {
        $this->gateCheckOut = $gateCheckOut;
    }

}
