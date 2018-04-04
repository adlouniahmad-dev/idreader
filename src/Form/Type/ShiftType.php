<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/13/2018
 * Time: 9:44 PM
 */

namespace App\Form\Type;


use App\Entity\Shift;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShiftType extends AbstractType
{

    private $session;
    private $em;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', ChoiceType::class, array(
                'attr' => ['class' => 'birthday-date'],
                'choices' => $this->setDays(),
                'label' => 'Day'
            ))
            ->add('startTime', TimeType::class, array(
                'attr' => ['class' => 'birthday-date'],
                'placeholder' => array(
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                ),
                'input' => 'datetime',
                'widget' => 'choice',
            ))
            ->add('endTime', TimeType::class, array(
                'attr' => ['class' => 'birthday-date'],
                'placeholder' => array(
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                ),
                'input' => 'datetime',
                'widget' => 'choice',
            ))
            ->add('save', SubmitType::class, array(
                'attr' => ['class' => 'btn green'],
                'label' => 'Add Shift'
            ))
            ->add('reset', ResetType::class, array(
                'attr' => ['class' => 'btn default'],
                'label' => 'Reset'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Shift::class,
        ));
    }

    private function setDays(): array
    {
        $days = array();
        $days['Sunday'] = 'Sunday';
        $days['Monday'] = 'Monday';
        $days['Tuesday'] = 'Tuesday';
        $days['Wednesday'] = 'Wednesday';
        $days['Thursday'] = 'Thursday';
        $days['Friday'] = 'Friday';
        $days['Saturday'] = 'Saturday';

        return $days;
    }

}