<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/7/2018
 * Time: 1:43 PM
 */

namespace App\Controller;


use App\Entity\Blacklist;
use App\Entity\Log;
use App\Entity\Office;
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

}