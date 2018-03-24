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
use App\Form\Type\AdminBuildingUpdateType;
use App\Form\Type\DeviceType;
use App\Form\Type\OfficeUpdateUserType;
use App\Form\Type\OfficeUserType;
use App\Form\Type\ScheduleType;
use App\Form\Type\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
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
                $guard = $this->getDoctrine()->getRepository(Guard::class)->findOneBy(['user' => $user]);
                $userDetails['guard'] = $guard;
            }

            if (in_array('Facility Administrator', $this->getUserRoles($user))) {
                $buildings = $this->getDoctrine()->getRepository(Building::class)->findOneBy(['admin' => $user]);
                $userDetails['buildings'] = $buildings;
            }

            if (in_array('Premise Owner', $this->getUserRoles($user))) {
                $offices = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['user' => $user]);
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

        return $this->render('errors/not_found.html.twig');
    }

    /**
     * @Route("/member/{userId}/edit", name="editProfile")
     * @param Request $request
     * @param Session $session
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editProfile(Request $request, Session $session, $userId)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user)
            return $this->render('errors/not_found.html.twig');

        // Create the form for the personal information
        $personalInfoForm = $this->createForm(UserType::class, $user, array(
            'action' => $this->generateUrl('editProfile', ['_fragment' => 'personal_info', 'userId' => $userId])
        ));
        $personalInfoForm->remove('role');
        $personalInfoForm->remove('building');
        $personalInfoForm->handleRequest($request);

        // Create the form for the building administrator info
        if (in_array('Facility Administrator', $this->getUserRoles($user))) {
            $buildingAdministratorForm = $this->createForm(AdminBuildingUpdateType::class, null, array(
                'action' => $this->generateUrl('editProfile', ['_fragment' => 'admin_info', 'userId' => $userId])
            ));
            $buildingAdministratorForm->handleRequest($request);
        } else {
            $buildingAdministratorForm = null;
        }

        // Create the form for the device info (Security Guard - MAC Address)
        if (in_array('Security Guard', $this->getUserRoles($user))) {
            $guard = $entityManager->getRepository(Guard::class)->findOneBy(['user' => $user]);
            $device = $guard->getDevice();
            $deviceForm = $this->createForm(DeviceType::class, $device, array(
                'action' => $this->generateUrl('editProfile', ['_fragment' => 'device_info', 'userId' => $userId])
            ));
            $deviceForm->handleRequest($request);
        } else {
            $deviceForm = null;
        }

        // Create the form for the office info (Premise Owner)
        if (in_array('Premise Owner', $this->getUserRoles($user))) {
            $buildings = $user->getBuildings();
            $building = $buildings->get(0);
            $officeForm = $this->createForm(OfficeUserType::class, null, array(
                'building' => $building,
                'action' => $this->generateUrl('editProfile', ['_fragment' => 'office_info', 'userId' => $userId])
            ));
            $officeForm->handleRequest($request);
        } else {
            $officeForm = null;
        }

        // Block that checks the submission of the personal info form
        if ($personalInfoForm->isSubmitted()) {
            $fragment = 'personal_info';
            if (!$personalInfoForm->isValid()) {
                $this->addFlash(
                    'danger',
                    'You have some errors. Please check below.'
                );
                return $this->renderEditProfilePage($user, $personalInfoForm, $deviceForm, $officeForm, $buildingAdministratorForm, $fragment);
            }
            $entityManager->flush();
            $this->addFlash(
                'success',
                'User personal info updated successfully!'
            );
            return $this->renderEditProfilePage($user, $personalInfoForm, $deviceForm, $officeForm, $buildingAdministratorForm, $fragment);
        }

        // Block that checks the submission of the building info form (admin)
        if ($buildingAdministratorForm !== null) {
            if ($buildingAdministratorForm->isSubmitted()) {
                $fragment = 'admin_info';
                if (!$buildingAdministratorForm->isValid()) {
                    $this->addFlash(
                        'danger',
                        'You have some errors. Please check below.'
                    );
                    return $this->renderEditProfilePage($user, $personalInfoForm, $deviceForm, $officeForm, $buildingAdministratorForm, $fragment);
                }
                $building = $buildingAdministratorForm->get('building')->getData();
                $building->setAdmin($user);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    'Administrator info updated successfully!'
                );
                return $this->renderEditProfilePage($user, $personalInfoForm, $deviceForm, $officeForm, $buildingAdministratorForm, $fragment);
            }
        }

        // Block that checks the submission of the office info form
        if ($officeForm !== null) {
            if ($officeForm->isSubmitted()) {
                $fragment = 'office_info';
                if (!$officeForm->isValid()) {
                    $this->addFlash(
                        'danger',
                        'You have some errors. Please check below.'
                    );
                    return $this->renderEditProfilePage($user, $personalInfoForm, $deviceForm, $officeForm, $buildingAdministratorForm, $fragment);
                }

                $oldOffice = $entityManager->getRepository(Office::class)->findOneBy(['user' => $user]);
                $oldOffice->removeUser();
                $entityManager->flush();

                $newOffice = $officeForm->get('office')->getData();
                $newOffice->setUser($user);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Office info updated successfully!'
                );
                return $this->renderEditProfilePage($user, $personalInfoForm, $deviceForm, $officeForm, $buildingAdministratorForm, $fragment);
            }
        }

        // Block that checks the submission of the device form
        if ($deviceForm !== null) {
            if ($deviceForm->isSubmitted()) {
                $fragment = 'device_info';
                if (!$deviceForm->isValid()) {
                    $this->addFlash(
                        'danger',
                        'You have some errors. Please check below.'
                    );
                    return $this->renderEditProfilePage($user, $personalInfoForm, $deviceForm, $officeForm, $buildingAdministratorForm, $fragment);
                }
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Office info updated successfully!'
                );

                return $this->renderEditProfilePage($user, $personalInfoForm, $deviceForm, $officeForm, $buildingAdministratorForm, $fragment);
            }
        }

        return $this->renderEditProfilePage($user, $personalInfoForm, $deviceForm, $officeForm, $buildingAdministratorForm);
    }

    /**
     * @param User $user
     * @param $personalInfoForm
     * @param $deviceForm
     * @param $officeForm
     * @param $buildingAdministratorForm
     * @param $fragment
     * @return Response
     */
    private function renderEditProfilePage(User $user, $personalInfoForm, $deviceForm, $officeForm, $buildingAdministratorForm, $fragment = null)
    {
        return $this->render('manageMembers/editUserProfile.twig', array(
            'user' => $user,
            'personalInfoForm' => $personalInfoForm->createView(),
            'deviceForm' => $deviceForm !== null ? $deviceForm->createView() : null,
            'officeForm' => $officeForm !== null ? $officeForm->createView() : null,
            'buildingAdministratorForm' => $buildingAdministratorForm !== null ? $buildingAdministratorForm->createView() : null,
            'fragment' => $fragment
        ));
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
     * @Route("/manageMembers/search", name="userAdvancedSearch")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function advancedSearch(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) || !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        return $this->render('manageMembers/searchMembers.html.twig');
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

    /**
     * @param $role
     * @return string
     */
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