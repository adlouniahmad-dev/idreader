<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleRepository")
 */
class Schedule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Guard")
     * @ORM\JoinColumn(name="guard_id", referencedColumnName="id")
     */
    private $guard;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gate")
     * @ORM\JoinColumn(name="gate_id", referencedColumnName="id")
     */
    private $gate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Shift")
     * @ORM\JoinColumn(name="shift_id", referencedColumnName="id")
     */
    private $shift;

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
    public function getGuard()
    {
        return $this->guard;
    }

    /**
     * @param mixed $guard
     */
    public function setGuard($guard): void
    {
        $this->guard = $guard;
    }

    /**
     * @return mixed
     */
    public function getGate()
    {
        return $this->gate;
    }

    /**
     * @param mixed $gate
     */
    public function setGate($gate): void
    {
        $this->gate = $gate;
    }

    /**
     * @return mixed
     */
    public function getShift()
    {
        return $this->shift;
    }

    /**
     * @param mixed $shift
     */
    public function setShift($shift): void
    {
        $this->shift = $shift;
    }

}
