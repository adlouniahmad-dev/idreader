<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 1/27/2018
 * Time: 2:19 PM
 */

namespace App\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueUserEmail
 * @package App\Validator\Constraints
 * @Annotation
 */
class UniqueUserEmail extends Constraint
{
    public $message = 'Gmail already exists.';
}