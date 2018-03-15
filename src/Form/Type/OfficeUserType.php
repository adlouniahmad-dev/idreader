<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/14/2018
 * Time: 12:50 PM
 */

namespace App\Form\Type;


use App\Entity\Building;
use App\Entity\Office;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OfficeUserType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('office', ChoiceType::class, array(
                'placeholder' => 'Select',
                'choices' => $this->getOffices($options['building']),
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('save', SubmitType::class, array(
                'attr' => ['class' => 'btn green'],
                'label' => 'Add Office'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'building' => null,
        ));
    }

    private function getOffices(Building $building)
    {
        if ($building) {
            $offices = $this->em->getRepository(Office::class)->findBy(array(
                'building' => $building,
                'user' => null,
            ));

            $officeArray = array();
            foreach ($offices as $office)
                $officeArray[$office->getOfficeNb() . ' - Floor: ' . $office->getFloorNb()] = $office;

            return $officeArray;
        }

        return null;
    }

}