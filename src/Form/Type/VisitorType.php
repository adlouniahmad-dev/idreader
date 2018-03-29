<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/29/2018
 * Time: 5:46 PM
 */

namespace App\Form\Type;


use App\Entity\Visitor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisitorType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ssn', TextType::class, array(
                'attr' => ['class' => 'form-control'],
                'label' => 'Social Security Number',
            ))
            ->add('firstName', TextType::class, array(
                'attr' => ['class' => 'form-control'],
                'label' => 'First Name',
            ))
            ->add('middleName', TextType::class, array(
                'attr' => ['class' => 'form-control'],
                'label' => 'Middle Name',
            ))
            ->add('lastName', TextType::class, array(
                'attr' => ['class' => 'form-control'],
                'label' => 'Last Name',
            ))
            ->add('nationality', CountryType::class)
            ->add('documentType', ChoiceType::class, array(
                'choices' => array(
                    'ID Card' => 'ID Card',
                    'Passport' => 'Passport',
                    'Other' => 'Other',
                ),
                'label' => 'Document Type'
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Visitor::class
        ));
    }
}