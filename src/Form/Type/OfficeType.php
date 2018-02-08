<?php
/**
 * Created by PhpStorm.
 * UserForm: Ahmad Adlouni
 * Date: 1/26/2018
 * Time: 4:10 PM
 */

namespace App\Form\Type;


use App\Entity\Building;
use App\Entity\Office;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficeType extends AbstractType
{

    private $session;
    private $em;
    private $buildings;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->em = $em;
        $this->buildings = array();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->setBuildingChoices();

        $builder
            ->add('officeNb', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g. B216'],
                'label' => 'Office Number',
            ))
            ->add('building', ChoiceType::class, array(
                'choices' => $this->buildings
            ))
            ->add('save', SubmitType::class, array(
                'attr' => ['class' => 'btn green'],
                'label' => 'Add Office',
            ))
            ->add('reset', ResetType::class, array(
                'attr' => ['class' => 'btn default'],
                'label' => 'Reset',
            ));

        $formModifier = function (FormInterface $form, Building $building = null) {
            $floors = null === $building ? array() : $building->getFloors();

            $form->add('floorNb', ChoiceType::class, array(
                'label' => 'Floor Number',
                'choices' => $floors
            ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm(), reset($this->buildings));
            }
        );

        $builder->get('building')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $building = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $building);
            }
        );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Office::class
        ));
    }

    public function setBuildingChoices(): void
    {
        if (in_array('fowner', $this->session->get('roles'))) {
            $result = $this->em->getRepository(Building::class)->findAll();
            foreach ($result as $building)
                $this->buildings[$building->getName()] = $building;

        } else if (in_array('fadmin', $this->session->get('roles'))) {
            $result = $this->em->getRepository(Building::class)->findBy(['admin' => $this->session->get('user')->getId()]);
            foreach ($result as $building)
                $this->buildings[$building->getName()] = $building;
        }
    }


}