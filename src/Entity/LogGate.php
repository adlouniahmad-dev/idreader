<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogGateRepository")
 */
class LogGate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gate")
     * @ORM\JoinColumn(name="gate_id", referencedColumnName="id")
     */
    private $gate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Log")
     * @ORM\JoinColumn(name="log_id", referencedColumnName="id")
     */
    private $log;

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

}
