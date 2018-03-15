<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/13/2018
 * Time: 11:52 PM
 */

namespace App\Form\Type;


use App\Entity\Building;
use App\Entity\Gate;
use App\Entity\Schedule;
use App\Entity\Shift;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ScheduleType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shift', ChoiceType::class, array(
                'choices' => $this->getShifts()
            ))
            ->add('gate', ChoiceType::class, array(
                'choices' => $this->getGates($options['building'])
            ))
            ->add('device', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g. aa:88:44:f3:ab:58'],
                'label' => 'MAC Address',
                'mapped' => false,
                'constraints' => array(
                    new NotBlank(),
                    new Regex(array(
                        'pattern' => '/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/',
                        'message' => 'MAC address must be consist of six groups of two hexadecimal digits, separated by colons :',
                    ))
                ),
            ))
            ->add('save', SubmitType::class, array(
                'attr' => ['class' => 'btn green'],
                'label' => 'Add Shift'
            ))
            ->add('reset', ResetType::class, array(
                'attr' => ['class' => 'btn default'],
                'label' => 'Reset'
            ));;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Schedule::class,
            'building' => null,
        ));
    }

    private function getShifts(): array
    {
        $shifts = $this->em->getRepository(Shift::class)->findAll();
        $shiftArray = array();

        foreach ($shifts as $shift) {
            $shiftArray[$shift->getDay() . ' | Start: ' . $shift->getStartTime()->format('h:i') . ' - End: ' . $shift->getEndTime()->format('h:i')] = $shift;
        }

        return $shiftArray;
    }

    private function getGates(Building $building)
    {
        if ($building) {
            $gates = $this->em->getRepository(Gate::class)->findBy(['building' => $building]);
            $gatesArray = array();

            foreach ($gates as $gate)
                $gatesArray[$gate->getName()] = $gate;

            return $gatesArray;
        }
        return null;
    }
}