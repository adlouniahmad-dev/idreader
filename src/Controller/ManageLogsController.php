<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/7/2018
 * Time: 1:43 PM
 */

namespace App\Controller;


use App\Entity\Blacklist;
use App\Entity\Building;
use App\Entity\Gate;
use App\Entity\Guard;
use App\Entity\Log;
use App\Entity\LogGate;
use App\Entity\LogGuard;
use App\Entity\Office;
use App\Entity\Schedule;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageLogsController extends Controller
{

    /**
     * @Route("/suspiciousVisits", name="suspiciousVisits")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function suspiciousList(Session $session)
    {

        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        return $this->render('logs/suspicious.html.twig');
    }

    /**
     * @Route("/suspicious/get", name="getSuspiciousList")
     * @throws DBALException
     */
    public function getSuspiciousList()
    {
        $suspiciousVisits = $this->getDoctrine()->getRepository(Log::class)->getSuspiciousVisits();
        $suspiciousList = array();
        if ($suspiciousVisits) {
            foreach ($suspiciousVisits as $suspiciousVisit) {
                $susInfo = array();
                $susInfo['firstName'] = $suspiciousVisit['first_name'];
                $susInfo['middleName'] = $suspiciousVisit['middle_name'];
                $susInfo['lastName'] = $suspiciousVisit['last_name'];
                $susInfo['date'] = $suspiciousVisit['date_created'];
                $susInfo['expected'] = (int)$suspiciousVisit['expected'] . ' min';
                $susInfo['realExit'] = (int)$suspiciousVisit['realExit'] . ' min';
                $susInfo['moreInfo'] = '<a href="' . $this->generateUrl('viewVisitor', ['visitorId' => $suspiciousVisit['id']])
                    . '" class="btn btn-sm btn-outline green margin-bottom-5"><i class="fa fa-search"></i> View</a>';

                array_push($suspiciousList, $susInfo);
            }
        }

        return $this->json(array('data' => $suspiciousList));
    }

    /**
     * @Route("/visits/today", name="todayVisits", methods={"GET"})
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getVisitsForToday(Session $session)
    {
        $user = $session->get('user');
        $office = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['user' => $user]);
        $visits = $this->getDoctrine()->getRepository(Log::class)->getVisitsOnPageLoad($office);

        if (!$visits)
            return $this->json(array('empty' => true));

        return $this->json(array('visitors' => $this->renderVisits($visits)));
    }

    /**
     * @Route("/visits/today/new/{lastLogId}", name="todayVisitsNew", methods={"GET"})
     * @param Session $session
     * @param $lastLogId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getVisitorsForTodayNewlyAdded(Session $session, $lastLogId)
    {
        $user = $session->get('user');
        $office = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['user' => $user]);
        $visits = $this->getDoctrine()->getRepository(Log::class)->getVisitsOnNewRecord($office, $lastLogId);

        if (!$visits)
            return $this->json(array('empty' => true));

        return $this->json(array('visitors' => $this->renderVisits($visits)));
    }

    private function renderVisits($visits)
    {
        $visitsArray = array();
        foreach ($visits as $visit) {
            $info = array();

            $info['visitorId'] = $visit->getVisitor()->getId();
            $info['visitorName'] = $visit->getVisitor()->getFullName();
            $info['logId'] = $visit->getId();
            $info['timeEntered'] = date_format($visit->getTimeEntered(), 'h:i A');

            $visitsArray[] = $info;
        }

        return $visitsArray;
    }

    /**
     * @Route("/visits/info/{logId}", name="visitorAndLogInfo", methods={"GET"})
     * @param $logId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getVisitorAndLogInfo($logId)
    {
        $log = $this->getDoctrine()->getRepository(Log::class)->find($logId);
        if (!$log)
            return $this->json(array('success' => false));

        $visitor = $log->getVisitor();
        $blacklisted = $this->getDoctrine()->getRepository(Blacklist::class)->findOneBy(['visitor' => $visitor]);

        $info = array(
            'visitor' => array(
                'fullName' => $visitor->getFullName(),
                'nationality' => $visitor->getCountry(),
                'idInfo' => $visitor->getDocumentType() . ', <b>SSN:</b> ' . $visitor->getSsn(),
                'blacklisted' => $blacklisted === null ? 'no' : 'yes',
            ),
            'log' => array(
                'timeEnteredBuilding' => date_format($log->getTimeEntered(), 'h:i A'),
                'EstimatedTime' => 'At ' . date_format($log->getEstimatedTime(), 'h:i A'),
            )
        );

        return $this->json($info);
    }

    /**
     * @Route("/visits/done/{logId}", name="doneVisit", methods={"PUT"})
     * @param $logId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function visitorDoneVisitingOffice($logId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $log = $entityManager->getRepository(Log::class)->find($logId);

        if (!$log)
            return $this->json(array('success' => false));

        $log->setDateLeftFromOffice(new \DateTime());
        $entityManager->flush();

        return $this->json(array('success' => true));
    }

    /**
     * @Route("/visits/getTotalVisitsPerDay", methods={"GET"})
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTotalVisitorsPerDay(Session $session)
    {
        $user = $session->get('user');
        $office = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['user' => $user]);
        $totalVisitsPerDay = $this->getDoctrine()->getRepository(Log::class)->getTotalVisitorsPerDay($office);

        return $this->json(array('count' => $totalVisitsPerDay[0][1]));
    }

    /**
     * @Route("/visits/doneVisitorsPerDay", methods={"GET"})
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function doneVisitorsPerDay(Session $session)
    {
        $user = $session->get('user');
        $office = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['user' => $user]);
        $DoneVisitsPerDay = $this->getDoctrine()->getRepository(Log::class)->getDoneVisitsPerDay($office);

        return $this->json(array('count' => $DoneVisitsPerDay[0][1]));
    }

    /**
     * @Route("/visits/getCountOfTotalVisits", methods={"GET"})
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getCountOfTotalVisits(Session $session)
    {
        $user = $session->get('user');
        $office = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['user' => $user]);
        $totalVisits = $this->getDoctrine()->getRepository(Log::class)->getCountTotalVisits($office);

        return $this->json(array('count' => $totalVisits[0][1]));
    }

    /**
     * @Route("/logs/view", name="viewLogs")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewLogs(Session $session)
    {

        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        return $this->render('logs/logs.html.twig');
    }

    /**
     * @Route("/logs/get", methods={"GET"})
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getLogs(Session $session)
    {
        if (in_array('fowner', $session->get('roles')))
            $logs = $this->getDoctrine()->getRepository(Log::class)->findAll();
        else if (in_array('fadmin', $session->get('roles'))) {
            $building = $session->get('user')->getBuildings()[0];
            $logs = $this->getDoctrine()->getRepository(Log::class)->findByBuilding($building);
        } else {
            $office = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['user' => $session->get('user')]);
            $logs = $this->getDoctrine()->getRepository(Log::class)->findBy(['office' => $office]);
        }

        $logsArray = array();
        foreach ($logs as $log) {

            $logGateEntrance = $this->getDoctrine()->getRepository(LogGate::class)->findOneBy(array(
                'log' => $log,
                'status' => 'entrance'
            ));
            $logGateExit = $this->getDoctrine()->getRepository(LogGate::class)->findOneBy(array(
                'log' => $log,
                'status' => 'exit'
            ));
            $logGuardEntrance = $this->getDoctrine()->getRepository(LogGuard::class)->findOneBy(array(
                'log' => $log,
                'status' => 'entrance'
            ));
            $logGuardExit = $this->getDoctrine()->getRepository(LogGuard::class)->findOneBy(array(
                'log' => $log,
                'status' => 'exit'
            ));

            $logInfo = array();
            $logInfo['visitorName'] = $log->getVisitor()->getFullName();
            $logInfo['office'] = $log->getOffice()->getOfficeNb();
            $logInfo['building'] = $log->getOffice()->getBuilding()->getName();
            $logInfo['date'] = date_format($log->getDateCreated(), 'jS F, Y');

            if (!in_array('powner', $session->get('roles'))) {
                $logInfo['timeEntered'] = $log->getTimeEntered() === null ? '' : date_format($log->getTimeEntered(), 'H:i A');
                $logInfo['checkInGuard'] = $logGuardEntrance->getGuard()->getUser()->getFullName();
                $logInfo['checkInGate'] = $logGateEntrance->getGate()->getName();
            }

            $logInfo['leftOfficeTime'] = $log->getDateLeftFromOffice() === null ? '' : date_format($log->getDateLeftFromOffice(), 'H:i A');

            if (!in_array('powner', $session->get('roles'))) {
                $logInfo['timeLeft'] = $log->getTimeExit() === null ? '' : date_format($log->getTimeExit(), 'H:i A');
                $logInfo['checkOutGuard'] = !$logGuardExit ? '' : $logGuardExit->getGuard()->getUser()->getFullName();
                $logInfo['checkOutGate'] = !$logGateExit ? '' : $logGateExit->getGate()->getName();
                $logInfo['estimatedTime'] = $log->getEstimatedTime() === null ? '' : date_format($log->getEstimatedTime(), 'H:i A');
            }

            array_push($logsArray, $logInfo);
        }

        return $this->json(array('data' => $logsArray));
    }

    /**
     * @Route("/logs/search", name="logsAdvancedSearch")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function advancedSearch(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        $buildingOptions = '';
        $officeOptions = '';
        $gateOptions = '';
        $guardOptions = '';

        if (in_array('fowner', $session->get('roles'))) {
            $buildings = $this->getDoctrine()->getRepository(Building::class)->findAll();

            $buildingOptions .= '<option value="-1">All</option>';
            foreach ($buildings as $building) {
                $buildingOptions .=
                    '<option value="' . $building->getId() . '">' . $building->getName() . '</option>';
            }

            $officeOptions = '<option value="-1">All</option>';
            $gateOptions = '<option value="-1">All</option>';
            $guardOptions = '<option value="-1">All</option>';

        } else if (in_array('fadmin', $session->get('roles'))) {

            $building = $this->getDoctrine()->getRepository(Building::class)->findOneBy(['admin' => $session->get('user')]);
            $buildingOptions .= '<option value="' . $building->getId() . '">' . $building->getName() . '</option>';

            $offices = $this->getDoctrine()->getRepository(Office::class)->findBy(['building' => $building]);
            $officeOptions = '<option value="-1">All</option>';
            foreach ($offices as $office) {
                $officeOptions
                    .= '<option value="' . $office->getId() . '">' . $office->getOfficeNb() . '</option>';
            }

            $gates = $this->getDoctrine()->getRepository(Gate::class)->findBy(['building' => $building]);
            $gateOptions = '<option value="-1">All</option>';
            foreach ($gates as $gate) {
                $gateOptions
                    .= '<option value="' . $gate->getId() . '">' . $gate->getName() . '</option>';
            }

            $guardOptions = '<option value="-1">All</option>';

        } else {
            $office = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['user' => $session->get('user')]);
            $officeOptions .= '<option value="' . $office->getId() . '">' . $office->getOfficeNb() . '</option>';

            $building = $office->getBuilding();
            $buildingOptions .= '<option value="' . $building->getId() . '">' . $building->getName() . '</option>';
        }

        return $this->render('logs/searchLogs.html.twig', array(
            'buildings' => $buildingOptions,
            'offices' => $officeOptions,
            'gates' => $gateOptions,
            'guards' => $guardOptions,
        ));
    }

    /**
     * @Route("/api/logs/search/building/{buildingId}")
     * @param $buildingId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function onBuildingChange($buildingId)
    {
        if ($buildingId == -1) {

            $officeOptions = '<option value="-1">All</option>';
            $gateOptions = '<option value="-1">All</option>';
            $guardOptions = '<option value="-1">All</option>';

        } else {

            $building = $this->getDoctrine()->getRepository(Building::class)->find($buildingId);
            $offices = $this->getDoctrine()->getRepository(Office::class)->findBy(['building' => $building]);
            $officeOptions = '<option value="-1">All</option>';
            foreach ($offices as $office) {
                $officeOptions
                    .= '<option value="' . $office->getId() . '">' . $office->getOfficeNb() . '</option>';
            }

            $gates = $this->getDoctrine()->getRepository(Gate::class)->findBy(['building' => $building]);
            $gateOptions = '<option value="-1">All</option>';
            foreach ($gates as $gate) {
                $gateOptions
                    .= '<option value="' . $gate->getId() . '">' . $gate->getName() . '</option>';
            }

            $guardOptions = '<option value="-1">All</option>';
        }

        $response = array(
            'offices' => $officeOptions,
            'gates' => $gateOptions,
            'guards' => $guardOptions
        );

        return $this->json($response);
    }

    /**
     * @Route("/api/logs/search/gate/{gateId}")
     * @param $gateId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function onGateChange($gateId)
    {
        if ($gateId == -1) {
            $guardOptions = '<option value="-1">All</option>';
        } else {

            $gate = $this->getDoctrine()->getRepository(Gate::class)->find($gateId);
            $schedules = $this->getDoctrine()->getRepository(Schedule::class)->findByGateGroupByGuard($gate);

            $guardOptions = '<option value="-1">All</option>';
            foreach ($schedules as $schedule) {
                $guardOptions .=
                    '<option value="' . $schedule->getGuard()->getId() . '">' . $schedule->getGuard()->getUser()->getFullName() . '</option>';
            }
        }

        return $this->json(array('guards' => $guardOptions));
    }

}