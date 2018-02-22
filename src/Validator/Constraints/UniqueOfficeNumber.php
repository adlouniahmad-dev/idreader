<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 2/11/2018
 * Time: 2:35 PM
 */

namespace App\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueOfficeNumber
 * @package App\Validator\Constraints
 *
 * @Annotation
 */
class UniqueOfficeNumber extends Constraint
{

    public $message = "Office Number already exists.";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}