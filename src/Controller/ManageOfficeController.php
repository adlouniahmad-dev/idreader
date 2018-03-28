<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 2/4/2018
 * Time: 2:16 PM
 */

namespace App\Controller;


use App\Entity\Building;
use App\Entity\Office;
use App\Form\Type\OfficeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageOfficeController extends Controller
{

    /**
     * @Route("/manageOffices/addOffice", name="addOffice")
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

    /**
     * @Route("/manageOffices/offices", name="viewOffices")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewOffices(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) || !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/not_found.html.twig');

        return $this->render('manageOffices/viewOffices.html.twig');
    }

    /**
     * @Route("/manageOffices/office/{officeId}", name="viewOffice")
     * @param Session $session
     * @param $officeId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewOffice(Session $session, $officeId)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) || !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/not_found.html.twig');

        $office = $this->getDoctrine()->getRepository(Office::class)->find($officeId);
        if (!$office)
            return $this->render('errors/not_found.html.twig');

        return $this->render('manageOffices/office.html.twig', array(
            'office' => $office,
        ));
    }

    /**
     * @Route("/manageOffices/office/{officeId}/edit", name="editOffice")
     * @param Session $session
     * @param Request $request
     * @param $officeId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editOffice(Session $session, Request $request, $officeId)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) || !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/not_found.html.twig');

        $entityManager = $this->getDoctrine()->getManager();
        $office = $entityManager->getRepository(Office::class)->find($officeId);
        if (!$office)
            return $this->render('errors/not_found.html.twig');

        $form = $this->createForm(OfficeType::class, $office);
        $form->remove('building');
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash(
                    'danger',
                    'You have some errors. Please check below.'
                );
                return $this->render('manageOffices/addOffice.html.twig', array(
                    'form' => $form->createView(),
                    'office' => $office,
                ));
            }

            $entityManager->flush();
            $this->addFlash(
                'success',
                'Office updated successfully!'
            );

            return $this->redirectToRoute('editOffice', array('officeId' => $officeId));
        }

        return $this->render('manageOffices/editOffice.html.twig', array(
            'office' => $office,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/manageOffices/search", name="officesAdvancedSearch")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function advancedSearch(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) || !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        return $this->render('manageOffices/searchOffices.html.twig');
    }

    /**
     * @Route("/api/office/getAllOffices/{page}", methods={"GET"})
     * @Route("/api/office/getAllOffices/{page}/{query}", methods={"GET"})
     * @param Session $session
     * @param int $page
     * @param string $query
     * @return JsonResponse
     */
    public function getOffices(Session $session, $page = 1, $query = '')
    {

        if (in_array('fowner', $session->get('roles')))
            $building = null;
        else
            $building = $this->getDoctrine()->getRepository(Building::class)->findOneBy(['admin' => $session->get('user')]);

        $currentPage = $page;

        $repo = $this->getDoctrine()->getRepository(Office::class);
        $offices = $repo->getAllOffices($currentPage, $query, $building);

        $totalOfficesReturned = $offices->getIterator()->count();
        $totalOffices = $offices->count();
        $limit = 10;
        $maxPages = ceil($totalOffices / $limit);

        $data = array();
        $data['totalOffices'] = $totalOffices;
        $data['totalOfficesReturned'] = $totalOfficesReturned;
        $data['limit'] = $limit;
        $data['currentPage'] = (int)$currentPage;
        $data['maxPages'] = $maxPages;

        $officesArray = array();

        foreach ($offices as $office) {
            $officeInfo = array();
            $officeInfo['id'] = $office->getId();
            $officeInfo['officeNb'] = $office->getOfficeNb();

            $officeInfo['member'] = array();
            $officeInfo['member']['name'] = $office->getUser() === null ? '-----' : $office->getUser()->getFullName();

            $officeInfo['building'] = $office->getBuilding()->getName();
            $officeInfo['floorNb'] = $office->getFloorNb();
            $officeInfo['dateCreated'] = date_format($office->getDateCreated(), 'jS F, Y, g:i a');

            $officesArray[] = $officeInfo;
        }
        $data['offices'] = $officesArray;

        return new JsonResponse($data);
    }

}