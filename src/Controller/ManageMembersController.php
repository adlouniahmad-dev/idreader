<?php
/**
 * Created by PhpStorm.
 * UserForm: Ahmad Adlouni
 * Date: 1/25/2018
 * Time: 12:40 PM
 */

namespace App\Controller;


use App\Entity\Building;
use App\Entity\Device;
use App\Entity\Guard;
use App\Entity\Office;
use App\Entity\User;
use App\Form\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageMembersController extends Controller
{

    /**
     * @Route("/manage-members/add-member", name="addMember")
     * @param Session $session
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMember(Session $session, Request $request)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if (!$form->isValid()) {

                $this->addFlash(
                    'danger',
                    'You have some errors. Please check below.'
                );
                return $this->render('manageMembers/addMember.html.twig', array(
                    'form' => $form->createView()
                ));
            }

            $em = $this->getDoctrine()->getManager();
            $role = $form['role']->getData();

            $user->addRole($role);
            $user->setDateCreated(new \DateTime());
            $em->persist($user);
            $em->flush();

            if ($role->getRoleName() == 'sguard') {

                $device = new Device();
                $device->setDateCreated(new \DateTime());
                $device->setMacAddress($form['device']->getData());
                $em->persist($device);
                $em->flush();

                $guard = new Guard();
                $guard->setUser($user);
                $guard->setDevice($device);
                $em->persist($guard);
                $em->flush();

            } else if ($role->getRoleName() == 'fadmin') {

                $building = $em->getRepository(Building::class)->find($form['building']->getData()->getId());
                $building->setAdmin($user);
                $em->flush();
            }

            $this->addFlash(
                'success',
                'Member added successfully!'
            );

            return $this->redirectToRoute('addMember');
        }

        return $this->render('manageMembers/addMember.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/user/{userId}", name="viewProfile")
     * @param Session $session
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewProfile(Session $session, $userId)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if ($user) {

            $userDetails = array();

            if (in_array('sguard', $this->getUserRoles($user))) {
                $guard = $this->getDoctrine()->getRepository(Guard::class)->findBy(['user' => $user]);
                $userDetails['guard'] = $guard;
            }

            if (in_array('fadmin', $this->getUserRoles($user))) {
                $buildings = $this->getDoctrine()->getRepository(Building::class)->findBy(['admin' => $user]);
                $userDetails['buildings'] = $buildings;
            }

            if (in_array('powner', $this->getUserRoles($user))) {
                $offices = $this->getDoctrine()->getRepository(Office::class)->findBy(['user' => $user]);
                $userDetails['offices'] = $offices;
            }

            /**
             * @var Building $buildings
             * @var Guard $guard
             * @var Office $offices
             */
            return $this->render('manageMembers/viewUserProfile.html.twig', array(
                'user' => $user,
                'roles' => $this->getUserRoles($user),
                'userDetails' => $userDetails
            ));
        }

        die('User not found.');
    }

    /**
     * @Route("/user/{userId}/edit", name="editProfile")
     * @param Session $session
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editProfile(Session $session, $userId)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if ($user) {
            $userForm = new User();
            $form = $this->createForm(UserType::class, $userForm);
            $form->remove('role');
            $form->remove('building');

            return $this->render('manageMembers/editUserProfile.twig', array(
                'user' => $user,
                'form' => $form->createView()
            ));
        }

        die('User not found');
    }

    /**
     * @param User $user
     * @return array
     */
    private function getUserRoles(User $user): array
    {
        $roles = $user->getRoles();
        $rolesArray = array();
        foreach ($roles as $value) {
            $rolesArray[] = $value->getRoleName();
        }

        return $rolesArray;
    }
}