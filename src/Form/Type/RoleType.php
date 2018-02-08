<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 1/26/2018
 * Time: 7:36 PM
 */

namespace App\Form\Type;


use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{

    private $session;
    private $em;
    private $choices;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->session = $session;
        $this->em = $em;
        $this->choices = array();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->setChoices();
        $builder->add('roleName', ChoiceType::class, array(
            'choices' => $this->choices,
            'label' => 'Role',
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Role::class,
        ));
    }

    private function setChoices()
    {
        $roles = '';

        if (in_array('fowner', $this->session->get('roles')))
            $roles = $this->em->getRepository(Role::class)->findAll();
        else if (in_array('fadmin', $this->session->get('roles')))
            $roles = $this->em->createQuery('select r from role r where r.roleName != "fowner" and r.roleName != "fadmin"')->getResult();

        if ($roles) {
            foreach ($roles as $role) {
                $roleName = $this->getRoleName($role);
                $this->choices[$roleName] = $role;
            }
        }
    }

    /**
     * @param Role $role
     * @return string
     */
    private function getRoleName(Role $role)
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
}