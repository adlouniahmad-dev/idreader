<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 2/11/2018
 * Time: 2:11 PM
 */

namespace App\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueGateName
 * @package App\Validator\Constraints
 *
 * @Annotation
 */
class UniqueGateName extends Constraint
{
    public $message = "This gate already exists.";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}