<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BuildingRepository")
 */
class Building
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
     * @Assert\Length(
     *     min="2",
     *     max="20",
     *     minMessage="Building name must be at least {{ limit }} characters long",
     *     maxMessage = "Building name cannot be longer than {{ limit }} characters"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $location;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[0-9]{1,3}$/",
     *     message="Starting floor must be a number"
     * )
     * @Assert\GreaterThanOrEqual(
     *     value="0",
     *     message="Starting floor must be greater than or equal to zero"
     * )
     * @Assert\Expression(
     *     "value <= this.getEndFloor()",
     *     message="Starting floor must be less than or equal to ending floor"
     * )
     */
    private $startFloor;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[0-9]{1,3}$/",
     *     message="Ending floor must be a number"
     * )
     * @Assert\LessThanOrEqual(
     *     value="163",
     *     message="Ending floor must not exceed {{ compared_value }} floor"
     * )
     */
    private $endFloor;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="admin", referencedColumnName="id")
     */
    private $admin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location): void
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getStartFloor()
    {
        return $this->startFloor;
    }

    /**
     * @param mixed $startFloor
     */
    public function setStartFloor($startFloor): void
    {
        $this->startFloor = $startFloor;
    }

    /**
     * @return mixed
     */
    public function getEndFloor()
    {
        return $this->endFloor;
    }

    /**
     * @param mixed $endFloor
     */
    public function setEndFloor($endFloor): void
    {
        $this->endFloor = $endFloor;
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
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param mixed $admin
     */
    public function setAdmin($admin): void
    {
        $this->admin = $admin;
    }

    public function getFloors()
    {
        $floors = array();
        for ($i = $this->startFloor; $i <= $this->endFloor; $i++) {
            $floors[$i] = $i;
        }
        return $floors;
    }

}
