<?php
/**
 * Created by PhpStorm.
 * UserForm: Ahmad Adlouni
 * Date: 1/24/2018
 * Time: 8:37 PM
 */

namespace App\Controller;


use App\Entity\Building;
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
     * @Route("/manage-buildings/add-building", name="addBuilding")
     * @param Session $session
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addBuilding(Session $session, Request $request)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');


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
     * @Route("/manage-buildings/all-buildings", name="viewBuildings")
     */
    public function viewBuildings()
    {
        return $this->render('manageBuildings/viewBuildings.html.twig');
    }

    /**
     * @Route("/manage-buildings/building/{buildingId}", requirements={"buildingId"="\d+"})
     * @param $buildingId
     * @return Response
     */
    public function viewBuilding($buildingId)
    {
        $building = $this->getDoctrine()->getRepository(Building::class)->find($buildingId);

        if (!$building)
            throw $this->createNotFoundException('Building Not found.');

        return $this->render('manageBuildings/building.html.twig', array(
            'building' => $building
        ));
    }

    /**
     * @Route("/api/getAllBuildings", name="getAllBuildings")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getAllBuildings()
    {
        $buildings = $this->getDoctrine()->getRepository(Building::class)->findAll();
        $buildingsArray = array();

        foreach ($buildings as $building) {
            $buildingInfo = array();
            $buildingInfo['id'] = $building->getId();
            $buildingInfo['name'] = $building->getName();
            $buildingInfo['location'] = $building->getLocation();
            $buildingInfo['admin'] = $building->getAdmin()->getFullName();
            $buildingInfo['dateCreated'] = $building->getDateCreated();

            $buildingsArray['buildings'][] = $buildingInfo;
        }

        return new JsonResponse($buildingsArray);
    }

}