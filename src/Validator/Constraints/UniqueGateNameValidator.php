<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 2/11/2018
 * Time: 2:12 PM
 */

namespace App\Validator\Constraints;


use App\Entity\Gate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueGateNameValidator extends ConstraintValidator
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param $protocol
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($protocol, Constraint $constraint)
    {
        $building = $protocol->getBuilding();
        $gateName = $protocol->getName();

        $conflicts = $this->em->getRepository(Gate::class)->findBy(array(
            'building' => $building,
            'name' => $gateName
        ));

        if (count($conflicts) > 0) {
            $this->context->buildViolation($constraint->message)
                ->atPath('name')
                ->addViolation();
        }
    }
}