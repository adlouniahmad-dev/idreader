<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 1/27/2018
 * Time: 4:06 PM
 */

namespace App\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueUserPhone
 * @package App\Validator\Constraints
 * @Annotation
 */
class UniqueUserPhone extends Constraint
{
    public $msgPhoneExists = 'Phone number already exists.';

    public $msgPhoneInvalid = 'Phone number is invalid. Please note that it should be a Lebanese number.';
}