<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogsSearchHistoryRepository")
 */
class LogsSearchHistory
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
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $role;

    /**
     * @ORM\Column(type="string")
     */
    private $visitorName;

    /**
     * @ORM\Column(type="string")
     */
    private $building;

    /**
     * @ORM\Column(type="string")
     */
    private $office;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $entranceGate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $exitGate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $entranceGuard;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $exitGuard;

    /**
     * @ORM\Column(type="string")
     */
    private $dateFrom;

    /**
     * @ORM\Column(type="string")
     */
    private $dateTo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $timeEnteredFrom;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $timeEnteredTo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $timeExitFrom;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $timeExitTo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $timeLeftFromOfficeFrom;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $timeLeftFromOfficeTo;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
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
    public function getEntranceGate()
    {
        return $this->entranceGate;
    }

    /**
     * @param mixed $entranceGate
     */
    public function setEntranceGate($entranceGate): void
    {
        $this->entranceGate = $entranceGate;
    }

    /**
     * @return mixed
     */
    public function getExitGate()
    {
        return $this->exitGate;
    }

    /**
     * @param mixed $exitGate
     */
    public function setExitGate($exitGate): void
    {
        $this->exitGate = $exitGate;
    }

    /**
     * @return mixed
     */
    public function getEntranceGuard()
    {
        return $this->entranceGuard;
    }

    /**
     * @param mixed $entranceGuard
     */
    public function setEntranceGuard($entranceGuard): void
    {
        $this->entranceGuard = $entranceGuard;
    }

    /**
     * @return mixed
     */
    public function getExitGuard()
    {
        return $this->exitGuard;
    }

    /**
     * @param mixed $exitGuard
     */
    public function setExitGuard($exitGuard): void
    {
        $this->exitGuard = $exitGuard;
    }

    /**
     * @return mixed
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param mixed $dateFrom
     */
    public function setDateFrom($dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return mixed
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param mixed $dateTo
     */
    public function setDateTo($dateTo): void
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @return mixed
     */
    public function getTimeEnteredFrom()
    {
        return $this->timeEnteredFrom;
    }

    /**
     * @param mixed $timeEnteredFrom
     */
    public function setTimeEnteredFrom($timeEnteredFrom): void
    {
        $this->timeEnteredFrom = $timeEnteredFrom;
    }

    /**
     * @return mixed
     */
    public function getTimeEnteredTo()
    {
        return $this->timeEnteredTo;
    }

    /**
     * @param mixed $timeEnteredTo
     */
    public function setTimeEnteredTo($timeEnteredTo): void
    {
        $this->timeEnteredTo = $timeEnteredTo;
    }

    /**
     * @return mixed
     */
    public function getTimeExitFrom()
    {
        return $this->timeExitFrom;
    }

    /**
     * @param mixed $timeExitFrom
     */
    public function setTimeExitFrom($timeExitFrom): void
    {
        $this->timeExitFrom = $timeExitFrom;
    }

    /**
     * @return mixed
     */
    public function getTimeExitTo()
    {
        return $this->timeExitTo;
    }

    /**
     * @param mixed $timeExitTo
     */
    public function setTimeExitTo($timeExitTo): void
    {
        $this->timeExitTo = $timeExitTo;
    }

    /**
     * @return mixed
     */
    public function getTimeLeftFromOfficeFrom()
    {
        return $this->timeLeftFromOfficeFrom;
    }

    /**
     * @param mixed $timeLeftFromOfficeFrom
     */
    public function setTimeLeftFromOfficeFrom($timeLeftFromOfficeFrom): void
    {
        $this->timeLeftFromOfficeFrom = $timeLeftFromOfficeFrom;
    }

    /**
     * @return mixed
     */
    public function getTimeLeftFromOfficeTo()
    {
        return $this->timeLeftFromOfficeTo;
    }

    /**
     * @param mixed $timeLeftFromOfficeTo
     */
    public function setTimeLeftFromOfficeTo($timeLeftFromOfficeTo): void
    {
        $this->timeLeftFromOfficeTo = $timeLeftFromOfficeTo;
    }

}
