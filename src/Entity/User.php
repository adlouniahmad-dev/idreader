<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as UserAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="2",
     *     max="30",
     *     minMessage="Member first name must be at least {{ limit }} characters long.",
     *     maxMessage="Member first name cannot be longer than {{ limit }} characters."
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Za-z][a-zA-Z ]+$/",
     *     message="Member first name must consists only of letters"
     * )
     */
    private $givenName;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="2",
     *     max="30",
     *     minMessage="Member last name must be at least {{ limit }} characters long.",
     *     maxMessage="Member last name cannot be longer than {{ limit }} characters."
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Za-z][a-zA-Z ]+$/",
     *     message="Member last name must consists only of letters."
     * )
     */
    private $familyName;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[A-Za-z0-9](\.?[A-Za-z0-9]){5,}@g(oogle)?mail\.com$/",
     *     message="Gmail account must consist of letters, numbers or dots then @gmail.com."
     * )
     * @UserAssert\UniqueUserEmail()
     */
    private $gmail;

    /**
     * @ORM\Column(type="date")
     */
    private $dob;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(
     *     min="8",
     *     max="8",
     *     exactMessage="Phone number must be 8 numbers."
     * )
     * @UserAssert\UniqueUserPhone()
     */
    private $phoneNb;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $imageUrl;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="users_roles")
     */
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

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
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param mixed $given_name
     */
    public function setGivenName($given_name): void
    {
        $this->givenName = $given_name;
    }

    /**
     * @return mixed
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * @param mixed $family_name
     */
    public function setFamilyName($family_name): void
    {
        $this->familyName = $family_name;
    }

    /**
     * @return mixed
     */
    public function getGmail()
    {
        return $this->gmail;
    }

    /**
     * @param mixed $gmail
     */
    public function setGmail($gmail): void
    {
        $this->gmail = $gmail;
    }

    /**
     * @return mixed
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @param mixed $dob
     */
    public function setDob($dob): void
    {
        $this->dob = $dob;
    }

    /**
     * @return mixed
     */
    public function getPhoneNb()
    {
        return $this->phoneNb;
    }

    /**
     * @param mixed $phone_nb
     */
    public function setPhoneNb($phone_nb): void
    {
        $this->phoneNb = $phone_nb;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $date_created
     */
    public function setDateCreated($date_created): void
    {
        $this->dateCreated = $date_created;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $role
     */
    public function addRole(Role $role): void
    {
        $role->addUser($this);
        $this->roles->add($role);
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param mixed $imageUrl
     */
    public function setImageUrl($imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }


}
