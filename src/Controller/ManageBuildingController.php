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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ManageBuildingController extends Controller
{

    /**
     * @Route("/manage-buildings/add-building", name="addBuilding")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addBuilding(Request $request, ValidatorInterface $validator)
    {
        $building = new Building();
        $form = $this->createForm(BuildingType::class, $building);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $data = $form->getData();

            if (!$form->isValid()) {
                $errors = $validator->validate($building);

                if ($data->getStartFloor() > $data->getEndFloor()) {
                    return $this->render('manageBuildings/addBuilding.html.twig', array(
                        'error' => "Starting floor must be less than or equal to ending floor",
                        'errors' => $errors,
                        'form' => $form->createView()
                    ));
                }
                else {
                    return $this->render('manageBuildings/addBuilding.html.twig', array(
                        'errors' => $errors,
                        'form' => $form->createView()
                    ));
                }
            }

            if ($data->getStartFloor() > $data->getEndFloor()) {
                return $this->render('manageBuildings/addBuilding.html.twig', array(
                    'error' => "Starting floor must be less than or equal to ending floor",
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
        return $this->render('manageBuildings/addBuilding.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/manage-buildings/view-buildings", name="viewBuildings")
     */
    public function viewBuildings()
    {

    }

}