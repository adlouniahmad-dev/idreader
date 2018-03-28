<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 2/9/2018
 * Time: 4:12 PM
 */

namespace App\Controller;


use App\Entity\Building;
use App\Entity\Gate;
use App\Entity\Schedule;
use App\Form\Type\GateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageGatesController extends Controller
{
    /**
     * @Route("/manageGates/addGate", name="addGate")
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

    /**
     * @Route("/manageGates/viewGates", name="viewGates")
     * @param Session $session
     */
    public function viewGates(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) || !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        if (in_array('fowner', $session->get('roles'))) {
            $buildings = $this->getDoctrine()->getRepository(Building::class)->findAll();
            $gates = $this->getDoctrine()->getRepository(Gate::class)->findAll();
        } else {
            $buildings = $this->getDoctrine()->getRepository(Building::class)->findOneBy(['admin' => $session->get('user')]);
            $gates = $this->getDoctrine()->getRepository(Gate::class)->findBy(['building' => $buildings]);
        }

        $data = array();
        foreach ($buildings as $building) {
            $data[$building->getName()] = array();
            foreach ($gates as $gate) {
                if ($gate->getBuilding()->getId() === $building->getId()) {
                    $data[$building->getName()][$gate->getName()] = array();
                    $data[$building->getName()][$gate->getName()]['gate_id'] = $gate->getId();
                    $guards = $this->getDoctrine()->getRepository(Schedule::class)->findBy(['gate' => $gate]);
                    $data[$building->getName()][$gate->getName()]['guards'] = array();
                    foreach ($guards as $guard) {
                        $data[$building->getName()][$gate->getName()]['guards'][] = $guard->getGuard()->getUser();
                    }
                }
            }
        }

        return $this->render('manageGates/viewGates.html.twig', array(
            'data' => $data
        ));
    }

    /**
     * @Route("/manageGates/gate/{gateId}", name="viewGate")
     * @param Session $session
     * @param $gateId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewGate(Session $session, $gateId)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) || !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        $gate = $this->getDoctrine()->getRepository(Gate::class)->find($gateId);
        if (!$gate)
            return $this->render('errors/not_found.html.twig');

        $schedules = $this->getDoctrine()->getRepository(Schedule::class)->findBy(['gate' => $gate]);
        $guards = array();
        foreach ($schedules as $schedule)
            $guards[] = $schedule->getGuard();

        return $this->render('manageGates/gate.html.twig', array(
            'gate' => $gate,
            'guards' => $guards,
        ));
    }

    /**
     * @Route("/manageGates/gate/{gateId}/edit", name="editGate")
     * @param Request $request
     * @param Session $session
     * @param $gateId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editGate(Request $request, Session $session, $gateId)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) || !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        $entityManager = $this->getDoctrine()->getManager();
        $gate = $entityManager->getRepository(Gate::class)->find($gateId);

        if (!$gate)
            return $this->render('errors/not_found.html.twig');

        $form = $this->createForm(GateType::class, $gate);
        $form->remove('building');
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash(
                    'danger',
                    'You have some errors. Please check below.'
                );
                return $this->render('manageGates/editGate.html.twig', array(
                    'gate' => $gate,
                    'form' => $form->createView(),
                ));
            }

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Gate updated successfully!'
            );

            return $this->redirectToRoute('editGate', array('gateId' => $gateId));

        }

        return $this->render('manageGates/editGate.html.twig', array(
            'gate' => $gate,
            'form' => $form->createView(),
        ));
    }

}