<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 2/4/2018
 * Time: 2:16 PM
 */

namespace App\Controller;


use App\Entity\Building;
use App\Entity\Log;
use App\Entity\LogGate;
use App\Entity\LogGuard;
use App\Entity\Office;
use App\Form\Type\OfficeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

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

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

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

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

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

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        if (in_array('fowner', $session->get('roles'))) {
            $buildings = $this->getDoctrine()->getRepository(Building::class)->findAll();
        } else {
            $buildings = $this->getDoctrine()->getRepository(Building::class)->findBy(['admin' => $session->get('user')]);
        }

        $floors = $buildings[0]->getFloors();

        return $this->render('manageOffices/searchOffices.html.twig', array(
            'buildings' => $buildings,
            'floors' => $floors,
        ));
    }

    /**
     * @Route("/api/office/search", name="searchOffices", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOfficesAdvancedSearch(Request $request)
    {
        $office_id = $request->query->get('office_id');
        $dateCreated = $request->query->get('office_date_created');
        $officeNumber = $request->query->get('office_number');
        $memberName = $request->query->get('member_name');
        $buildingId = $request->query->get('office_building');
        $floorNumber = $request->query->get('floor_number');

        $offices = $this->getDoctrine()->getRepository(Office::class)->advancedSearch($office_id, $dateCreated, $officeNumber,
            $memberName, $buildingId, $floorNumber);

        $iTotalRecords = count($offices);
        $iDisplayLength = intval($request->query->get('length'));
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($request->query->get('start'));
        $sEcho = intval($request->query->get('draw'));

        $records = array();
        $records['data'] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        for ($i = $iDisplayStart; $i < $end; $i++) {
            $id = ($i + 1);
            $records['data'][] = array(
                '<input type="checkbox" name="id[]" value="' . $id . '">',
                $offices[$i]['id'],
                $offices[$i]['date_created'],
                $offices[$i]['office_nb'],
                $offices[$i]['member'],
                $offices[$i]['name'],
                $offices[$i]['floor_nb'],
                '<a href="' . $this->generateUrl('viewOffice', ['officeId' => $offices[$i]['id']]) . '" class="btn btn-sm btn-outline grey-salsa"><i class="fa fa-search"></i> View</a>',
            );
        }

        $records['draw'] = $sEcho;
        $records['recordsTotal'] = $iTotalRecords;
        $records['recordsFiltered'] = $iTotalRecords;

        return new JsonResponse($records);
    }

    /**
     * @Route("/api/office/getBuildingFloors/{buildingId}", methods={"GET"}, name="getBuildingFloors")
     * @param $buildingId
     * @return Response
     */
    public function getBuildingFloors($buildingId)
    {
        $building = $this->getDoctrine()->getRepository(Building::class)->find($buildingId);
        if (!$building)
            return new Response('Building not found', Response::HTTP_NOT_FOUND);

        $floors = $building->getFloors();
        $options = '<option value="">All</option>';
        foreach ($floors as $key => $floor)
            $options .= '<option value="' . $key . '">' . $floor . '</option>';

        return new Response($options, Response::HTTP_OK);
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
            $officeInfo['member']['name'] = $office->getUser() === null ? '' : $office->getUser()->getFullName();

            $officeInfo['building'] = $office->getBuilding()->getName();
            $officeInfo['floorNb'] = $office->getFloorNb();
            $officeInfo['dateCreated'] = date_format($office->getDateCreated(), 'jS F, Y, g:i a');

            $officesArray[] = $officeInfo;
        }
        $data['offices'] = $officesArray;

        return new JsonResponse($data);
    }

    /**
     * @Route("/manageOffice/office/{officeId}/edit/delete", name="deleteOffice")
     * @param $officeId
     * @return JsonResponse
     */
    public function deleteOffice($officeId)
    {
        $error = array('success' => 'no');
        $success = array('success' => 'yes');

        $entityManager = $this->getDoctrine()->getManager();
        $office = $entityManager->getRepository(Office::class)->find($officeId);

        if (!$office)
            return $this->json($error);

        $logs = $entityManager->getRepository(Log::class)->findBy(['office' => $office]);
        if ($logs) {
            foreach ($logs as $log) {
                $logGates = $entityManager->getRepository(LogGate::class)->findBy(['log' => $log]);
                if ($logGates) {
                    foreach ($logGates as $logGate) {
                        $entityManager->remove($logGate);
                        $entityManager->flush();
                    }
                }

                $logGuards = $entityManager->getRepository(LogGuard::class)->findBy(['log' => $log]);
                if ($logGuards) {
                    foreach ($logGuards as $logGuard) {
                        $entityManager->remove($logGuard);
                        $entityManager->flush();
                    }
                }
            }
        }

        try {

            $entityManager->remove($office);
            $entityManager->flush();

        } catch (\Exception $e) {
            return $this->json($error);
        }

        return $this->json($success);
    }

}