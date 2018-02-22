<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 2/9/2018
 * Time: 4:14 PM
 */

namespace App\Form\Type;


use App\Entity\Building;
use App\Entity\Gate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GateType extends AbstractType
{

    private $em;
    private $session;
    private $buildingChoices;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->session = $session;
        $this->buildingChoices = array();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->setBuildings();

        $builder
            ->add('name', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g. South Gate'],
                'label' => 'Gate Name',
            ))
            ->add('building', ChoiceType::class, array(
                'choices' => $this->buildingChoices,
            ))
            ->add('save', SubmitType::class, array(
                'attr' => ['class' => 'btn green'],
                'label' => 'Add Gate',
            ))
            ->add('reset', ResetType::class, array(
                'attr' => ['class' => 'btn default'],
                'label' => 'Reset',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Gate::class,
        ));
    }

    private function setBuildings(): void
    {
        if (in_array('fowner', $this->session->get('roles'))) {
            $result = $this->em->getRepository(Building::class)->findAll();
            foreach ($result as $building)
                $this->buildingChoices[$building->getName()] = $building;

        } else if (in_array('fadmin', $this->session->get('roles'))) {
            $result = $this->em->getRepository(Building::class)->findBy(['admin' => $this->session->get('user')->getId()]);
            foreach ($result as $building)
                $this->buildingChoices[$building->getName()] = $building;
        }
    }

}