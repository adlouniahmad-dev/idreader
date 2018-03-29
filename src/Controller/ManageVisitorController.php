<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/29/2018
 * Time: 4:57 PM
 */

namespace App\Controller;


use App\Entity\Blacklist;
use App\Entity\Visitor;
use App\Form\Type\VisitorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageVisitorController extends Controller
{

    /**
     * @Route("/visitors", name="viewVisitors")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewVisitors(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        return $this->render('visitors/viewVisitors.html.twig');
    }

    /**
     * @Route("/visitor/{visitorId}", name="viewVisitor")
     * @param Session $session
     * @param $visitorId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewVisitor(Session $session, $visitorId)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        $visitor = $this->getDoctrine()->getRepository(Visitor::class)->find($visitorId);
        if (!$visitor)
            return $this->render('errors/not_found.html.twig');

        $visitorBlacklist = $this->getDoctrine()->getRepository(Blacklist::class)->findOneBy(['visitor' => $visitor]);

        return $this->render('visitors/visitor.html.twig', array(
            'visitor' => $visitor,
            'visitorBlacklist' => $visitorBlacklist
        ));
    }

    /**
     * @Route("/visitor/{visitorId}/settings", name="visitorSettings")
     * @param Session $session
     * @param Request $request
     * @param $visitorId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function visitorSettings(Session $session, Request $request, $visitorId)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        $entityManager = $this->getDoctrine()->getManager();
        $visitor = $entityManager->getRepository(Visitor::class)->find($visitorId);
        if (!$visitor)
            return $this->render('errors/not_found.html.twig');

        $form = $this->createForm(VisitorType::class, $visitor);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash(
                    'danger',
                    'You have some errors. Please check below.'
                );
                return $this->render('visitors/editVisitor.html.twig', array(
                    'form' => $form->createView(),
                    'visitor' => $visitor,
                ));
            }

            $entityManager->flush();
            $this->addFlash(
                'success',
                'Visitor\'s info updated successfully.'
            );
        }

        return $this->render('visitors/editVisitor.html.twig', array(
            'visitor' => $visitor,
            'form' => $form->createView()
        ));
    }

}