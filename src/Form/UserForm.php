<?php
/**
 * Created by PhpStorm.
 * UserForm: Ahmad Adlouni
 * Date: 1/26/2018
 * Time: 4:40 PM
 */

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;


class UserForm
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="2",
     *     max="30",
     *     minMessage="Member first name must be at least {{ limit }} characters long",
     *     maxMessage="Member first name cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Z][a-zA-Z ]+$/",
     *     message="Member first name must consists only of letters"
     * )
     */
    private $givenName;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="2",
     *     max="30",
     *     minMessage="Member last name must be at least {{ limit }} characters long",
     *     maxMessage="Member last name cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Z][a-zA-Z ]+$/",
     *     message="Member last name must consists only of letters"
     * )
     */
    private $familyName;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-z0-9](\.?[a-z0-9]){5,}@g(oogle)?mail\.com$/",
     *     message="Gmail account must contain letters, numbers and dots"
     * )
     */
    private $gmail;

    private $dob;

    /**
     * @Assert\Length(
     *     min="8",
     *     max="8",
     *     minMessage="Phone number must be 8 numbers"
     *     maxMessage="Phone number must not exceed 8 numbers"
     * )
     */
    private $phoneNb;

    /**
     * @Assert\NotBlank()
     */
    private $userRole;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})",
     *     message="MAC address must be consist of six groups of two hexadecimal digits, separated by colons :"
     * )
     */
    private $macAddress;

    /**
     * @return mixed
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param mixed $givenName
     */
    public function setGivenName($givenName): void
    {
        $this->givenName = $givenName;
    }

    /**
     * @return mixed
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * @param mixed $familyName
     */
    public function setFamilyName($familyName): void
    {
        $this->familyName = $familyName;
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
     * @param mixed $phoneNb
     */
    public function setPhoneNb($phoneNb): void
    {
        $this->phoneNb = $phoneNb;
    }

    /**
     * @return mixed
     */
    public function getUserRole()
    {
        return $this->userRole;
    }

    /**
     * @param mixed $userRole
     */
    public function setUserRole($userRole): void
    {
        $this->userRole = $userRole;
    }

    /**
     * @return mixed
     */
    public function getMacAddress()
    {
        return $this->macAddress;
    }

    /**
     * @param mixed $macAddress
     */
    public function setMacAddress($macAddress): void
    {
        $this->macAddress = $macAddress;
    }


}