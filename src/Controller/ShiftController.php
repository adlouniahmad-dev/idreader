<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/13/2018
 * Time: 10:10 PM
 */

namespace App\Controller;


use App\Entity\Shift;
use App\Form\Type\ShiftType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ShiftController extends Controller
{

    /**
     * @Route("/shifts/add", name="addShift")
     * @param Request $request
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addShift(Request $request, Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        $shift = new Shift();
        $form = $this->createForm(ShiftType::class, $shift);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if (!$form->isValid()) {
                $this->addFlash(
                    'danger',
                    'You have some errors. Please check below.'
                );
                return $this->render('shifts/addShift.html.twig', array(
                    'form' => $form->createView()
                ));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($shift);
            $em->flush();

            $this->addFlash(
                'success',
                'Shift added successfully!'
            );

            return $this->redirectToRoute('addShift');
        }

        return $this->render('shifts/addShift.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/shifts/view", name="viewShifts")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewShifts(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        $shifts = $this->getDoctrine()->getRepository(Shift::class)->findAll();

        return $this->render('shifts/viewShifts.html.twig', array(
            'shifts' => $shifts,
        ));
    }
}