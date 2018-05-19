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
use App\Entity\Log;
use App\Entity\LogGate;
use App\Entity\LogGuard;
use App\Entity\Schedule;
use App\Form\Type\GateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewGates(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        if (in_array('fowner', $session->get('roles'))) {
            $buildings = $this->getDoctrine()->getRepository(Building::class)->findAll();
            $gates = $this->getDoctrine()->getRepository(Gate::class)->findAll();
        } else {
            $buildings = $this->getDoctrine()->getRepository(Building::class)->findBy(['admin' => $session->get('user')]);
            $gates = $this->getDoctrine()->getRepository(Gate::class)->findBy(['building' => $buildings]);
        }

        $data = array();
        foreach ($buildings as $building) {
            $data[$building->getName()] = array();
            foreach ($gates as $gate) {
                if ($gate->getBuilding()->getId() === $building->getId()) {
                    $data[$building->getName()][$gate->getName()] = array();
                    $data[$building->getName()][$gate->getName()]['gate_id'] = $gate->getId();
                    $guards = $this->getDoctrine()->getRepository(Schedule::class)->findByGateGroupByGuard($gate);
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

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        $gate = $this->getDoctrine()->getRepository(Gate::class)->find($gateId);
        if (!$gate)
            return $this->render('errors/not_found.html.twig');

        $schedules = $this->getDoctrine()->getRepository(Schedule::class)->findByGateGroupByGuard($gate);
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

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
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

    /**
     * @Route("/manageGate/gate/{gateId}/edit/delete", name="deleteGate")
     * @param $gateId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteGate($gateId)
    {
        $error = array('success' => 'no');
        $success = array('success' => 'yes');

        $entityManager = $this->getDoctrine()->getManager();
        $gate = $entityManager->getRepository(Gate::class)->find($gateId);

        if (!$gate)
            return $this->json($error);

        $logGates = $entityManager->getRepository(LogGate::class)->findBy(['gate' => $gate]);
        if ($logGates) {
            $logs = array();
            foreach ($logGates as $logGate) {
                $log = $entityManager->getRepository(Log::class)->find($logGate->getLog()->getId());
                if ($log) {
                    array_push($logs, $log);
                    $logGuards = $entityManager->getRepository(LogGuard::class)->findBy(['log' => $log]);
                    if ($logGuards) {
                        foreach ($logGuards as $logGuard) {
                            $entityManager->remove($logGuard);
                            $entityManager->flush();
                        }
                    }
                }
                $entityManager->remove($logGate);
                $entityManager->flush();
            }

            if (!empty($logs)) {
                foreach ($logs as $log) {
                    $entityManager->remove($log);
                    $entityManager->flush();
                }
            }
        }

        $schedules = $entityManager->getRepository(Schedule::class)->findBy(['gate' => $gate]);
        if ($schedules) {
            foreach ($schedules as $schedule) {
                $entityManager->remove($schedule);
                $entityManager->flush();
            }
        }

        try {

            $entityManager->remove($gate);
            $entityManager->flush();

        } catch (\Exception $e) {
            return $this->json($error);
        }

        return $this->json($success);
    }

}