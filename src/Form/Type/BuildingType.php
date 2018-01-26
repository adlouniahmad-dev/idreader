<?php
/**
 * Created by PhpStorm.
 * UserForm: Ahmad Adlouni
 * Date: 1/24/2018
 * Time: 8:39 PM
 */

namespace App\Form\Type;


use App\Entity\Building;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'Building Name'],
                'label' => 'Building Name',
                'label_attr' => ['class' => 'col-md-3 control-label'],
                'required' => false
            ))
            ->add('location', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'Building Address'],
                'label' => 'Building Address',
                'label_attr' => ['class' => 'col-md-3 control-label'],
                'required' => false
            ))
            ->add('startFloor', IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'Starting Floor'],
                'label' => 'Starting Floor',
                'label_attr' => ['class' => 'col-md-3 control-label'],
                'required' => false
            ))
            ->add('endFloor', IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ending Floor'],
                'label' => 'Ending Floor',
                'label_attr' => ['class' => 'col-md-3 control-label'],
                'required' => false
            ))
            ->add('save', SubmitType::class, array(
                'attr' => ['class' => 'btn green'],
                'label' => 'Add Building'
            ))
            ->add('reset', ResetType::class, array(
                'attr' => ['class' => 'btn default'],
                'label' => 'Reset'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Building::class,
        ));
    }
}