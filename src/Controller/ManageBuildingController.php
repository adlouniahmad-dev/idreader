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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
                    )
                );
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
     * @Route("/manage-buildings/view-buildings", name="viewBuildings")
     */
    public function viewBuildings()
    {

    }


}