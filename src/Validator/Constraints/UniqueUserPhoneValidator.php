<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 1/27/2018
 * Time: 4:07 PM
 */

namespace App\Validator\Constraints;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserPhoneValidator extends ConstraintValidator
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value != '') {

            if (!$this->checkAreaCode($value)) {
                $this->context->buildViolation($constraint->msgPhoneInvalid)
                    ->addViolation();
            }

//            else {
//                $conflicts = $this->em->getRepository(User::class)->findBy(['phoneNb' => $value]);
//
//                if (count($conflicts) > 0) {
//                    $this->context->buildViolation($constraint->msgPhoneExists)
//                        ->addViolation();
//                }
//            }
        }
    }

    public function checkAreaCode($value)
    {
        $areaCode = array('03', '70', '71', '76', '78', '79', '81');

        if (in_array(substr($value, 0, 2), $areaCode)) {
            return true;
        }
        return false;
    }
}