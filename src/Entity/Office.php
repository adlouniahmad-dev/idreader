<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as OfficeAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OfficeRepository")
 * @OfficeAssert\UniqueOfficeNumber()
 */
class Office
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $officeNb;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $floorNb;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Building")
     * @ORM\JoinColumn(name="building_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $building;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
    public function getOfficeNb()
    {
        return $this->officeNb;
    }

    /**
     * @param mixed $officeNb
     */
    public function setOfficeNb($officeNb): void
    {
        $this->officeNb = $officeNb;
    }

    /**
     * @return mixed
     */
    public function getFloorNb()
    {
        return $this->floorNb;
    }

    /**
     * @param mixed $floorNb
     */
    public function setFloorNb($floorNb): void
    {
        $this->floorNb = $floorNb;
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

    public function removeUser(): void
    {
        $this->user = null;
    }

}
