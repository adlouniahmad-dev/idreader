<?php
/**
 * Created by PhpStorm.
 * UserForm: Ahmad Adlouni
 * Date: 1/24/2018
 * Time: 3:09 PM
 */

namespace App\Form\Type;

use App\Entity\Building;
use App\Entity\Office;
use App\Entity\Role;
use App\Entity\User;
use App\Validator\Constraints\UniqueAdminBuildingValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{

    private $session;
    private $em;
    private $roleChoices;
    private $buildingChoices;
    private $officeChoices;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->em = $em;
        $this->roleChoices = array();
        $this->buildingChoices = array();
        $this->officeChoices = array();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->setRoleChoices();
        $this->setBuildingChoices();

        $builder
            ->add('givenName', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g. Ahmad'],
                'label' => 'First Name',
            ))
            ->add('familyName', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g. Adlouni'],
                'label' => 'Last Name',
            ))
            ->add('gmail', EmailType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g. example@gmail.com'],
                'label' => 'Gmail',
            ))
            ->add('phoneNb', TextType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g. 71387643'],
                'label' => 'Phone Number'
            ))
            ->add('dob', BirthdayType::class, array(
                'label' => 'Date of Birth',
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                )
            ))
            ->add('role', ChoiceType::class, array(
                'choices' => $this->roleChoices,
                'mapped' => false,
            ))
            ->add('building', ChoiceType::class, array(
                'choices' => $this->buildingChoices,
                'label' => 'Building',
                'mapped' => false,
            ))
            ->add('save', SubmitType::class, array(
                'attr' => ['class' => 'btn green'],
                'label' => 'Add Member'
            ))
            ->add('reset', ResetType::class, array(
                'attr' => ['class' => 'btn default'],
                'label' => 'Reset'
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }

    private function setRoleChoices(): void
    {
        $roles = '';

        if (in_array('fowner', $this->session->get('roles')))
            $roles = $this->em->createQuery("select r from App\Entity\Role r where r.roleName != 'fowner'")->getResult();
        else if (in_array('fadmin', $this->session->get('roles')))
            $roles = $this->em->createQuery("select r from App\Entity\Role r where r.roleName != 'fowner' and r.roleName != 'fadmin'")->getResult();

        if ($roles) {
            foreach ($roles as $role) {
                $roleName = $this->getRoleName($role);
                $this->roleChoices[$roleName] = $role;
            }
        }
    }

    /**
     * @param Role $role
     * @return string
     */
    private function getRoleName(Role $role): string
    {
        if ($role->getRoleName() == 'fadmin')
            $roleName = 'Facility Administrator';
        else if ($role->getRoleName() == 'fowner')
            $roleName = 'Facility Owner';
        else if ($role->getRoleName() == 'powner')
            $roleName = 'Premise Owner';
        else
            $roleName = 'Security Guard';

        return $roleName;
    }

    private function setBuildingChoices(): void
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

    /**
     * @param Building $building
     */
    private function setOfficeChoices(Building $building): void
    {

        $offices = $this->em->getRepository(Office::class)->findBy(['building' => $building]);
        if ($offices) {
            foreach ($offices as $office)
                $this->officeChoices[$office->getOfficeNb()] = $office;
        }
    }

}