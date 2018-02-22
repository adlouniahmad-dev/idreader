<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 2/11/2018
 * Time: 2:37 PM
 */

namespace App\Validator\Constraints;


use App\Entity\Office;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueOfficeNumberValidator extends ConstraintValidator
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
        $officeNb = $protocol->getOfficeNb();
        $building = $protocol->getBuilding();

        $conflicts = $this->em->getRepository(Office::class)->findBy(array(
            'officeNb' => $officeNb,
            'building' => $building
        ));

        if (count($conflicts) > 0) {
            $this->context->buildViolation($constraint->message)
                ->atPath('officeNb')
                ->addViolation();
        }
    }
}