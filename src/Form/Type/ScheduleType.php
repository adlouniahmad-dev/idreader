<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/13/2018
 * Time: 11:52 PM
 */

namespace App\Form\Type;


use App\Entity\Schedule;
use App\Entity\Shift;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'choices' => $this->setShifts()
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Schedule::class,
        ));
    }

    private function setShifts(): array
    {
        $shifts = $this->em->getRepository(Shift::class)->findAll();
        $shiftArray = array();

        foreach ($shifts as $shift) {
            $shiftArray[$shift->getDay() . ' - ' . $shift->getStartTime() . ' | ' . $shift->getEndTime()] = $shift;
        }

        return $shiftArray;
    }
}