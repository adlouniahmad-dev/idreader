<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 2/4/2018
 * Time: 2:16 PM
 */

namespace App\Controller;


use App\Entity\Office;
use App\Form\Type\OfficeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageOfficeController extends Controller
{

    /**
     * @Route("/manage-offices/add-office", name="addOffice")
     * @param Session $session
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addOffice(Session $session, Request $request)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        $office = new Office();
        $form = $this->createForm(OfficeType::class, $office);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if (!$form->isValid()) {
                $this->addFlash(
                    'danger',
                    'You have some errors. Please check below.'
                );
                return $this->render('manageOffices/addOffice.html.twig', array(
                    'form' => $form->createView()
                ));
            }

            $office->setDateCreated(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($office);
            $em->flush();

            $this->addFlash(
                'success',
                'Office added successfully!'
            );

            return $this->redirectToRoute('addOffice');
        }

        return $this->render('manageOffices/addOffice.html.twig', array(
            'form' => $form->createView()
        ));
    }

}