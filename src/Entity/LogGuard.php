<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogGuardRepository")
 */
class LogGuard
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Log")
     * @ORM\JoinColumn(name="log_id", referencedColumnName="id")
     */
    private $log;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

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
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param mixed $log
     */
    public function setLog($log): void
    {
        $this->log = $log;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

}
