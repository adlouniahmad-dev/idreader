<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/24/2018
 * Time: 5:12 PM
 */

namespace App\Form\Type;

use App\Entity\Building;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminBuildingUpdateType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('building', ChoiceType::class, array(
                'choices' => $this->setBuildings(),
                'label' => 'Building',
            ))
            ->add('save', SubmitType::class, array(
                'attr' => ['class' => 'btn green'],
                'label' => 'Save'
            ))
            ->add('reset', ResetType::class, array(
                'attr' => ['class' => 'btn default'],
                'label' => 'Reset'
            ));
    }

    private function setBuildings(): array
    {
        $buildings = $this->em->getRepository(Building::class)->findAll();
        $buildingsArray = array();
        foreach ($buildings as $building)
            $buildingsArray[$building->getName()] = $building;

        return $buildingsArray;
    }
}