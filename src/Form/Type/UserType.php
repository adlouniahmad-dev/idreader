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
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('givenName', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'First Name'],
                'label' => 'First Name',
            ))
            ->add('familyName', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'Last Name'],
                'label' => 'Last Name',
            ))
            ->add('gmail', EmailType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g. example@gmail.com'],
                'label' => 'Gmail Account',
            ))
            ->add('phoneNb', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g. 71387643'],
                'label' => 'Phone Number',
                'required' => false
            ))
            ->add('dob', BirthdayType::class, array(
                'label' => 'Date of Birth',
            ))
            ->add('device', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g. aa:88:44:f3:ab:58'],
                'label' => 'MAC Address',
                'mapped' => false
            ))
            ->add('save', SubmitType::class, array(
                'attr' => ['class' => 'btn green'],
                'label' => 'Add Member'
            ))
            ->add('reset', ResetType::class, array(
                'attr' => ['class' => 'btn default'],
                'label' => 'Reset'
            ));

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $roleChoices = [];

                if (in_array('fowner', $this->session->get('roles'))) {
                    $roleChoices = array(
                        'Select' => '',
                        'Facility Administrator' => 'fadmin',
                        'Premise Owner' => 'powner',
                        'Security Guard' => 'sguard'
                    );
                } else if (in_array('fadmin', $this->session->get('roles'))) {
                    $roleChoices = array(
                        'Select' => '',
                        'Premise Owner' => 'powner',
                        'Security Guard' => 'sguard'
                    );
                }

                $form->add('role', ChoiceType::class, array(
                    'mapped' => false,
                    'choices' => $roleChoices
                ));
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }

}