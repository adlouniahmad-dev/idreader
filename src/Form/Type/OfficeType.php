<?php
/**
 * Created by PhpStorm.
 * UserForm: Ahmad Adlouni
 * Date: 1/26/2018
 * Time: 4:10 PM
 */

namespace App\Form\Type;


use App\Entity\Office;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('officeNb', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'Office Number'],
                'label' => 'Office Number',
                'label_attr' => ['class' => 'col-md-3 control-label'],
                'required' => false
            ))
            ->add('floorNb', IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'Floor Number'],
                'label' => 'Floor Number',
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
            'data_class' => Office::class
        ));
    }
}