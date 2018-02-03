<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 1/27/2018
 * Time: 3:55 PM
 */

namespace App\Validator\Constraints;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserEmailValidator extends ConstraintValidator
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
        $conflicts = $this->em->getRepository(User::class)->findBy(['gmail' => $value]);

        if (count($conflicts) > 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}