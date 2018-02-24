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
use App\SSP;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/member/{userId}", name="viewProfile", requirements={"userId"="\d+"})
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

            if (in_array('Security Guard', $this->getUserRoles($user))) {
                $guard = $this->getDoctrine()->getRepository(Guard::class)->findBy(['user' => $user]);
                $userDetails['guard'] = $guard;
            }

            if (in_array('Facility Administrator', $this->getUserRoles($user))) {
                $buildings = $this->getDoctrine()->getRepository(Building::class)->findBy(['admin' => $user]);
                $userDetails['buildings'] = $buildings;
            }

            if (in_array('Premise Owner', $this->getUserRoles($user))) {
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
     * @Route("/member/{userId}/edit", name="editProfile", requirements={"userId"="\d+"})
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
     * @Route("/manage-members/view-members", name="viewUsers")
     * @param Session $session
     * @return Response
     */
    public function viewUsers(Session $session)
    {

        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        return $this->render('manageMembers/viewUsers.html.twig');

    }

    /**
     * @Route("/api/getAllUsers", name="getAllUsers")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getAllUsers()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $usersArray = array();

        foreach ($users as $user) {
            $userInfo = array();
            $userInfo['id'] = $user->getId();
            $userInfo['name'] = $user->getFullName();
            $userInfo['gmail'] = $user->getGmail();
            $userInfo['dob'] = date_format($user->getDob(), 'jS F, Y');

            $userRoles = $this->getUserRoles($user);
            $userRolesText = implode('<br>', $userRoles);

            $userInfo['role'] = $userRolesText;
            $userInfo['dateCreated'] = date_format($user->getDateCreated(), 'jS F, Y, g:i a');

            $usersArray['users'][] = $userInfo;
        }

        return new JsonResponse($usersArray);
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
            $rolesArray[] = $this->getRoleName($value);
        }

        return $rolesArray;
    }

    private function getRoleName($role): string
    {
        if ($role->getRoleName() == 'fowner')
            return 'Facility Owner';
        else if ($role->getRoleName() == 'fadmin')
            return 'Facility Administrator';
        else if ($role->getRoleName() == 'powner')
            return 'Premise Owner';
        else
            return 'Security Guard';
    }
}