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
use App\Entity\Schedule;
use App\Entity\User;
use App\Form\Type\OfficeUserType;
use App\Form\Type\ScheduleType;
use App\Form\Type\UserType;
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
     * @Route("/manageMembers/addMember", name="addMember")
     * @param Session $session
     * @param Request $request
     * @return Request|Response
     */
    public function addMember(Session $session, Request $request)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) || !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

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
            $building = $form['building']->getData();

            $user->addRole($role);
            $user->addBuilding($building);
            $user->setDateCreated(new \DateTime());
            $em->persist($user);
            $em->flush();

            if ($role->getRoleName() == 'sguard') {

                $guard = new Guard();
                $guard->setUser($user);
                $em->persist($guard);
                $em->flush();

                return $this->redirectToRoute('addSecurityGuard', array(
                    'userId' => $user->getId(),
                    'buildingId' => $building->getId()
                ));

            } else if ($role->getRoleName() == 'fadmin') {

                $building = $em->getRepository(Building::class)->find($form['building']->getData()->getId());
                $building->setAdmin($user);
                $em->flush();

            } else if ($role->getRoleName() == 'powner') {

                return $this->redirectToRoute('addPremiseOwner', array(
                    'userId' => $user->getId(),
                    'buildingId' => $building->getId()
                ));

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
     * @Route("/manageMembers/addMember/addSecurityGuard/{userId}/{buildingId}", name="addSecurityGuard")
     * @param Request $request
     * @param $userId
     * @param $buildingId
     * @return Response
     */
    public function addSecurityGuard(Request $request, $userId, $buildingId)
    {
        if ($request->headers->get('referer')) {

            $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
            $building = $this->getDoctrine()->getRepository(Building::class)->find($buildingId);

            $schedule = new Schedule();
            $form = $this->createForm(ScheduleType::class, $schedule, array(
                'building' => $building
            ));

            $form->handleRequest($request);
            if ($form->isSubmitted()) {

                if (!$form->isValid()) {
                    $this->addFlash(
                        'danger',
                        'You have some errors. Please check below.'
                    );
                    return $this->render('manageMembers/addSecurityGuard.html.twig', array(
                        'form' => $form->createView()
                    ));
                }

                $em = $this->getDoctrine()->getManager();

                $deviceMac = $form['device']->getData();
                $device = new Device();
                $device->setDateCreated(new \DateTime());
                $device->setMacAddress($deviceMac);
                $em->persist($device);
                $em->flush();

                $guard = $this->getDoctrine()->getRepository(Guard::class)->findOneBy(['user' => $user]);
                $guard->setDevice($device);
                $em->flush();

                $schedule->setGuard($guard);
                $em->persist($schedule);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Member added successfully!'
                );

                return $this->redirectToRoute('addMember');
            }

            return $this->render('manageMembers/addSecurityGuard.html.twig', array(
                'form' => $form->createView()
            ));
        }

        die('Error Occurred');
    }

    /**
     * @Route("/manageMembers/addMember/addPremiseOwner/{userId}/{buildingId}", name="addPremiseOwner")
     * @param Request $request
     * @param $userId
     * @param $buildingId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addPremiseOwner(Request $request, $userId, $buildingId)
    {
        if ($request->headers->get('referer')) {

            $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
            $building = $this->getDoctrine()->getRepository(Building::class)->find($buildingId);

            $form = $this->createForm(OfficeUserType::class, null, array(
                'building' => $building
            ));

            $form->handleRequest($request);
            if ($form->isSubmitted()) {

                if (!$form->isValid()) {
                    $this->addFlash(
                        'danger',
                        'You have some errors. Please check below.'
                    );
                    return $this->render('manageMembers/addPremiseOwner.html.twig', array(
                        'form' => $form->createView()
                    ));
                }

                $em = $this->getDoctrine()->getManager();
                $office = $form->get('office')->getData();

                $officeUpdate = $em->getRepository(Office::class)->find($office->getId());
                $officeUpdate->setUser($user);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Member added successfully!'
                );

                return $this->redirectToRoute('addMember');
            }

            return $this->render('manageMembers/addPremiseOwner.html.twig', array(
                'form' => $form->createView()
            ));
        }
        die('Error Occurred');
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

        if (!in_array('fowner', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

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
     * @Route("/manageMembers/viewMembers", name="viewUsers")
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
     * @param int $page
     * @param string $query
     * @return JsonResponse
     * @Route("/api/getAllUsers/{page}", name="getAllUsers", requirements={"page"="\d+"})
     * @Route("/api/getAllUsers/{page}/{query}")
     * @Method("GET")
     */
    public function getAllUsers($page = 1, $query = '')
    {
        $currentPage = $page;

        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->getAllUsers($currentPage, $query);

        $totalUsersReturned = $users->getIterator()->count();
        $totalUsers = $users->count();
        $limit = 10;
        $maxPages = ceil($totalUsers / $limit);

        $data = array();
        $data['totalUsers'] = $totalUsers;
        $data['totalUsersReturned'] = $totalUsersReturned;
        $data['limit'] = $limit;
        $data['currentPage'] = (int)$currentPage;
        $data['maxPages'] = $maxPages;

        $usersArray = array();

        foreach ($users as $user) {
            $userInfo = array();
            $userInfo['id'] = $user->getId();
            $userInfo['givenName'] = $user->getGivenName();
            $userInfo['familyName'] = $user->getFamilyName();
            $userInfo['gmail'] = $user->getGmail();
            $userInfo['dob'] = date_format($user->getDob(), 'jS F, Y');
            $userInfo['role'] = implode(',<br>', $this->getUserRoles($user));
            $userInfo['dateCreated'] = date_format($user->getDateCreated(), 'jS F, Y, g:i a');

            $usersArray[] = $userInfo;
        }
        $data['users'] = $usersArray;

        return new JsonResponse($data);
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