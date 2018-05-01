<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/31/2018
 * Time: 9:50 PM
 */

namespace App\Controller\Api;


use App\Country;
use App\Entity\Blacklist;
use App\Entity\Guard;
use App\Entity\Log;
use App\Entity\LogGate;
use App\Entity\LogGuard;
use App\Entity\Office;
use App\Entity\OfficeSettings;
use App\Entity\Schedule;
use App\Entity\Visitor;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as FRoute;

/**
 * Class VisitorRestController
 * @package App\Controller\Api
 * @FRoute("/api/visitor")
 */
class VisitorRestController extends Controller
{

    /**
     * Check the visitor if he exists in the database
     *
     * @Route("/check", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function checkVisitor(Request $request)
    {
        if ($request->request->get('ssn') === null)
            return $this->json(array(
                'success' => false,
                'message' => 'SSN Not Defined.'
            ), Response::HTTP_NOT_FOUND);

        $visitorSSN = $request->request->get('ssn');
        $visitor = $this->getDoctrine()->getRepository(Visitor::class)->findOneBy(['ssn' => $visitorSSN]);
        if (!$visitor)
            return $this->json(array(
                'success' => true,
                'found' => false,
                'message' => 'Visitor Not Found.'
            ), Response::HTTP_OK);

        $blacklisted = $this->getDoctrine()->getRepository(Blacklist::class)->findOneBy(['visitor' => $visitor]);
        if (!$blacklisted)
            $blacklist = false;
        else
            $blacklist = true;

        $visitorInfo = array(
            'id' => $visitor->getId(),
            'firstName' => $visitor->getFirstName(),
            'lastName' => $visitor->getLastName(),
            'nationality' => $visitor->getCountry(),
            'typeOfDocument' => $visitor->getDocumentType(),
            'ssn' => $visitor->getSsn(),
            'blacklist' => $blacklist
        );

        $response = array(
            'success' => true,
            'found' => true,
            'message' => 'Visitor Found.',
            'visitor' => $visitorInfo
        );

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * If visitor not found then call this API
     * to add the visitor + the log
     *
     * @Route("/add", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function addVisitor(Request $request)
    {
        $content = $request->getContent();
        if (!$content)
            return $this->json(array(
                'success' => false,
                'message' => 'Body is empty.',
            ), Response::HTTP_NOT_FOUND);

        $data = json_decode($content, true);
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $visitor = new Visitor();
            $visitor->setFirstName($data['firstName']);
            $visitor->setLastName($data['lastName']);
            $visitor->setDateCreated(new \DateTime());
            $visitor->setDocumentType($data['documentType']);
            $visitor->setSsn($data['ssn']);

            $country = new Country();
            $visitor->setNationality($country->getCountryCode($data['nationality']));

            $entityManager->persist($visitor);
            $entityManager->flush();

        } catch (\Exception $e) {

            return $this->json(array(
                'success' => false,
                'message' => 'Can\'t add visitor.',
            ), Response::HTTP_NOT_FOUND);
        }

        $officeId = $data['officeId'];
        $guardId = $data['guardId'];

        $log = $this->addLogToVisitor($visitor, $officeId);
        if ($log instanceof Log) {
            $this->addLogGate($log, $guardId);
            $this->addLogGuard($log, $guardId);
        } else {
            return $this->json(array(
                'success' => false,
                'message' => 'Error adding the log.'
            ), Response::HTTP_NOT_FOUND);
        }

        return $this->json(array(
            'success' => true,
            'message' => 'Visitor added successfully.',
        ), Response::HTTP_OK);
    }

    /**
     * If visitor exists in the in the database then
     * call this API to add only a log to him
     *
     * @Route("/add/log/{guardId}", methods={"POST"})
     * @param $guardId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addLog($guardId, Request $request)
    {
        if ($request->request->get('visitorId') === null || $request->request->get('officeId') === null)
            return $this->json(array(
                'success' => false,
                'message' => 'Visitor ID OR Office Id Not Defined.'
            ), Response::HTTP_NOT_FOUND);

        $visitorId = $request->request->get('visitorId');

        $entityManager = $this->getDoctrine()->getManager();
        $visitor = $entityManager->getRepository(Visitor::class)->find($visitorId);

        if (!$visitor)
            return $this->json(array(
                'success' => false,
                'message' => 'Visitor Not Found.'
            ), Response::HTTP_NOT_FOUND);

        $log = $this->addLogToVisitor($visitor, $request->request->get('officeId'));
        if ($log instanceof Log) {
            $this->addLogGate($log, $guardId);
            $this->addLogGuard($log, $guardId);
        } else {
            return $this->json(array(
                'success' => false,
                'message' => 'Error adding the log.'
            ), Response::HTTP_NOT_FOUND);
        }

        return $this->json(array(
            'success' => true,
            'message' => 'Log added successfully.'
        ), Response::HTTP_OK);
    }

    /**
     * This function is called from
     * addLog API &
     * addVisitor API
     *
     * @param Visitor $visitor
     * @param $officeId
     * @return Log|bool
     */
    private function addLogToVisitor(Visitor $visitor, $officeId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $office = $entityManager->getRepository(Office::class)->find($officeId);
        if (!$office)
            return false;

        try {
            $log = new Log();
            $log->setVisitor($visitor);
            $log->setOffice($office);
            $log->setDateCreated(new \DateTime());
            $log->setTimeEntered(new \DateTime());

            $getEstimatedTime = $this->setEstimationTimeOnCheckIn($office);
            if ($getEstimatedTime instanceof \DateTime) {
                $log->setEstimatedTime($getEstimatedTime);
            } else {
                $estimatedTime = (new \DateTime())->add(new \DateInterval('PT' . $getEstimatedTime . 'M'));
                $log->setEstimatedTime($estimatedTime);
            }

            $entityManager->persist($log);
            $entityManager->flush();

        } catch (\Exception $exception) {
            return false;
        }

        return $log;
    }

    /**
     * Add log gate to a visitor
     *
     * @param Log $log
     * @param $guardId
     * @param string $status
     * @return bool
     */
    private function addLogGate(Log $log, $guardId, $status = 'entrance')
    {
        $entityManager = $this->getDoctrine()->getManager();
        $guard = $entityManager->getRepository(Guard::class)->find($guardId);
        $schedule = $entityManager->getRepository(Schedule::class)->findOneBy(['guard' => $guard]);
        $gate = $schedule->getGate();

        try {
            $logGate = new LogGate();
            $logGate->setGate($gate);
            $logGate->setLog($log);
            $logGate->setStatus($status);

            $entityManager->persist($logGate);
            $entityManager->flush();

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Add log guard to a visitor
     *
     * @param Log $log
     * @param $guardId
     * @param string $status
     * @return bool
     */
    private function addLogGuard(Log $log, $guardId, $status = 'entrance')
    {
        $entityManager = $this->getDoctrine()->getManager();
        $guard = $entityManager->getRepository(Guard::class)->find($guardId);

        try {
            $logGuard = new LogGuard();
            $logGuard->setLog($log);
            $logGuard->setGuard($guard);
            $logGuard->setStatus($status);

            $entityManager->persist($logGuard);
            $entityManager->flush();

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * API to check out a visitor
     *
     * @Route("/update/log/checkOut/{guardId}", methods={"POST"})
     * @param $guardId
     * @param Request $request
     * @return bool|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateLog($guardId, Request $request)
    {
        if ($request->request->get('ssn') === null)
            return $this->json(array(
                'success' => false,
                'message' => 'Visitor ID Not Defined.'
            ), Response::HTTP_NOT_FOUND);

        $entityManager = $this->getDoctrine()->getManager();
        $visitorSsn = $request->get('ssn');
        $visitor = $entityManager->getRepository(Visitor::class)->findOneBy(['ssn' => $visitorSsn]);

        if (!$visitor)
            return $this->json(array(
                'success' => false,
                'message' => 'Visitor not found.'
            ), Response::HTTP_NOT_FOUND);

        try {
            $log = $entityManager->getRepository(Log::class)->getLastLogForSpecificVisitor($visitor);
        } catch (NoResultException | NonUniqueResultException $e) {
            return $this->json(array(
                'success' => false,
                'message' => 'Log not found.'
            ), Response::HTTP_NOT_FOUND);
        }

        try {

            $log->setTimeExit(new \DateTime());
            $entityManager->flush();

        } catch (\Exception $e) {
            return $this->json(array(
                'success' => false,
                'message' => 'Can\'t update the log.'
            ), Response::HTTP_NOT_FOUND);
        }

        $this->addLogGate($log, $guardId, 'exit');
        $this->addLogGuard($log, $guardId, 'exit');

        $this->setEstimationTimeOnCheckOut($log);

        return $this->json(array(
            'success' => true,
            'message' => 'Visitor checked out successfully.'
        ), Response::HTTP_OK);
    }

    /**
     * @param Office $office
     * @return float|int
     * @throws \Exception
     */
    public function setEstimationTimeOnCheckIn(Office $office)
    {
        $logs = $this->getDoctrine()->getRepository(Log::class)->getVisitsToday($office);
        $officeSettings = $this->getDoctrine()->getRepository(OfficeSettings::class)->findOneBy(['office' => $office]);

        $estimatedTime = ($officeSettings->getWalkTime() * 2) + $officeSettings->getAverageWaitingTime();
        if (!$logs)
            return $estimatedTime;

        $length = count($logs);
        $lastLogEstimated = $logs[$length - 1]->getEstimatedTime();
        $estimatedTime = $lastLogEstimated->add(new \DateInterval('PT' . $estimatedTime . 'M'));

        return $estimatedTime;
    }

    /**
     * @param Log $log
     */
    public function setEstimationTimeOnCheckOut(Log $log)
    {
        $office = $log->getOffice();
        $entityManager = $this->getDoctrine()->getManager();
        $logs = $entityManager->getRepository(Log::class)->getVisitsToday($office);

        if (!$logs)
            return;

        $estimatedTime = $log->getEstimatedTime();
        $timeExit = $log->getTimeExit();
        $diff = $timeExit->diff($estimatedTime);

        $flag = $estimatedTime > $timeExit ? true : false;

        foreach ($logs as $aLog) {
            $oldEstimatedTime = $aLog->getEstimatedTime();
            $newEstimatedTime = $flag ? $oldEstimatedTime->add($diff) : $oldEstimatedTime->sub($diff);

            $entityManager->refresh($aLog);
            $aLog->setEstimatedTime($newEstimatedTime);
            $entityManager->flush();
        }
        return;
    }

    /**
     * @param $minutes
     * @return \DateTime
     */
    private function convertToHoursMins($minutes)
    {
        $hrs = floor($minutes / 60);
        $min = $minutes % 60;
        $time = $hrs . ':' . $min . ':00';

        return new \DateTime($time);
    }

}