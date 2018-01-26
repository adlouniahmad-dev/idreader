<?php
/**
 * Created by PhpStorm.
 * UserForm: Ahmad Adlouni
 * Date: 1/24/2018
 * Time: 3:09 PM
 */

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('givenName', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'First Name'],
                'label' => 'First Name',
                'label_attr' => ['class' => 'col-md-3 control-label'],
                'required' => false
            ))
            ->add('familyName',TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'Last Name'],
                'label' => 'Last Name',
                'label_attr' => ['class' => 'col-md-3 control-label'],
                'required' => false
            ))
            ->add('gmail', EmailType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'Gmail Account'],
                'label' => 'Gmail Account',
                'label_attr' => ['class' => 'col-md-3 control-label'],
                'required' => false
            ))
            ->add('phoneNb', IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'Phone Number'],
                'label' => 'Phone Number',
                'label_attr' => ['class' => 'col-md-3 control-label'],
                'required' => false
            ))
            ->add('roles', CollectionType::class, array(
                'entry_type' => RoleType::class,
                'entry_options' => array(
                    'label' => false
                )
            ))
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }

}