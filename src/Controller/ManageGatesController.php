<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 2/9/2018
 * Time: 4:12 PM
 */

namespace App\Controller;


use App\Entity\Gate;
use App\Form\Type\GateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageGatesController extends Controller
{

    /**
     * @Route("/manage-gates/add-gate", name="addGate")
     * @param Session $session
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addGate(Session $session, Request $request)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        $gate = new Gate();
        $form = $this->createForm(GateType::class, $gate);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if (!$form->isValid()) {

                $this->addFlash(
                    'danger',
                    'You have some errors. Please check below.'
                );
                return $this->render('manageGates/addGate.html.twig', array(
                    'form' => $form->createView()
                ));
            }

            $gate->setDateCreated(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($gate);
            $em->flush();

            $this->addFlash(
                'success',
                'Gate added successfully!'
            );

            return $this->redirectToRoute('addGate');
        }

        return $this->render('manageGates/addGate.html.twig', array(
            'form' => $form->createView()
        ));
    }
}