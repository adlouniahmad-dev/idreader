<?php
/**
 * Created by PhpStorm.
 * UserForm: Ahmad Adlouni
 * Date: 1/24/2018
 * Time: 8:37 PM
 */

namespace App\Controller;


use App\Entity\Building;
use App\Entity\Gate;
use App\Entity\Office;
use App\Entity\User;
use App\Form\Type\BuildingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageBuildingController extends Controller
{

    /**
     * @Route("/manageBuildings/addBuilding", name="addBuilding")
     * @param Session $session
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addBuilding(Session $session, Request $request)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        $building = new Building();
        $form = $this->createForm(BuildingType::class, $building);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash(
                    'danger',
                    'You have some errors. Please check below.'
                );
                return $this->render('manageBuildings/addBuilding.html.twig', array(
                    'form' => $form->createView()
                ));
            }

            $building->setDateCreated(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($building);
            $em->flush();

            $this->addFlash(
                'success',
                'Building added successfully!'
            );

            return $this->redirectToRoute('addBuilding');
        }
        return $this->render('manageBuildings/addBuilding.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/manageBuildings/viewBuildings", name="viewBuildings")
     * @param Session $session
     * @return Response
     */
    public function viewBuildings(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) || !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        if (in_array('fowner', $session->get('roles')))
            $buildings = $this->getDoctrine()->getRepository(Building::class)->findAll();
        else
            $buildings = $this->getDoctrine()->getRepository(Building::class)->findBy(['user' => $session->get('user')]);

        return $this->render('manageBuildings/viewBuildings.html.twig', array(
            'buildings' => $buildings
        ));
    }

    /**
     * @Route("/manageBuildings/building/{buildingId}", requirements={"buildingId"="\d+"}, name="viewBuilding")
     * @param Session $session
     * @param $buildingId
     * @return Response
     */
    public function viewBuilding(Session $session, $buildingId)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        $building = $this->getDoctrine()->getRepository(Building::class)->find($buildingId);

        if (!$building)
            return $this->render('errors/not_found.html.twig');

        if (!in_array('fowner', $session->get('roles')) ||
            !in_array('fadmin', $session->get('roles')) &&
            $session->get('user')->getId() !== $building->getAdmin()->getId())
            return $this->render('errors/access_denied.html.twig');

        $gates = $this->getDoctrine()->getRepository(Gate::class)->findBy(['building' => $building]);

        return $this->render('manageBuildings/building.html.twig', array(
            'building' => $building,
            'gates' => $gates
        ));
    }

    /**
     * @Route("/manageBuildings/building/{buildingId}/edit", requirements={"buildingId"="\d+"}, name="editBuilding")
     * @param Request $request
     * @param Session $session
     * @param $buildingId
     * @return Response
     */
    public function editBuilding(Request $request, Session $session, $buildingId)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        $entityManager = $this->getDoctrine()->getManager();
        $building = $entityManager->getRepository(Building::class)->find($buildingId);
        if (!$building)
            return $this->render('errors/not_found.html.twig');

        $form = $this->createForm(BuildingType::class, $building);
        $form->remove('reset');
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash(
                    'danger',
                    'You have some errors. Please check below.'
                );
                return $this->render('manageBuildings/editBuilding.html.twig', array(
                    'building' => $building,
                    'form' => $form->createView()
                ));
            }

            $entityManager->flush();
            $this->addFlash(
                'success',
                'Building updated successfully!'
            );
            return $this->redirectToRoute('editBuilding', array(
                'buildingId' => $building->getId()
            ));
        }

        return $this->render('manageBuildings/editBuilding.html.twig', array(
            'building' => $building,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/api/offices/{buildingId}/{page}", name="getOfficesBuilding")
     * @param $buildingId
     * @param int $page
     * @Method("GET")
     * @return JsonResponse
     */
    public function getOffices($buildingId, $page = 1)
    {
        $building = $this->getDoctrine()->getRepository(Building::class)->find($buildingId);
        $currentPage = $page;

        $repo = $this->getDoctrine()->getRepository(Office::class);
        $offices = $repo->getAllOfficesBuilding($currentPage, $building);

        $totalOffices = $offices->count();
        $limit = 10;
        $maxPages = ceil($totalOffices / $limit);

        $data = array();
        $data['maxPages'] = $maxPages;

        $officesArray = array();

        foreach ($offices as $office) {
            $officeInfo = array();
            $officeInfo['id'] = $office->getId();
            $officeInfo['officeNumber'] = $office->getOfficeNb();

            $officesArray[] = $officeInfo;
        }
        $data['offices'] = $officesArray;

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/membersBuilding/{buildingId}/{page}")
     * @param $buildingId
     * @param int $page
     * @return JsonResponse
     */
    public function getMembers($buildingId, $page = 1)
    {
        $building = $this->getDoctrine()->getRepository(Building::class)->find($buildingId);
        $currentPage = $page;

        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->getAllUsersFromSpecificBuilding($currentPage, $building);

        $totalUsers = $users->count();
        $limit = 10;
        $maxPages = ceil($totalUsers / $limit);

        $data = array();
        $data['maxPages'] = $maxPages;

        $usersArray = array();

        foreach ($users as $user) {
            $userInfo = array();
            $userInfo['id'] = $user->getId();
            $userInfo['name'] = $user->getFullName();

            $usersArray[] = $userInfo;
        }
        $data['users'] = $usersArray;

        return new JsonResponse($data);
    }

}