<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 5/2/2018
 * Time: 5:24 PM
 */

namespace App\Controller;


use App\Entity\Building;
use App\Entity\LogsHistory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class LogsHistoryController extends Controller
{

    /**
     * @Route("/history/logs", name="logsHistory")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getHistory(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        return $this->render('logsHistory/logsHistory.html.twig');
    }

    /**
     * @Route("/api/history/logs", methods={"GET"})
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getLogsHistory(Session $session)
    {

        if (in_array('fowner', $session->get('roles')))
            $logs = $this->getDoctrine()->getRepository(LogsHistory::class)->findAll();
        else {
            $building = $this->getDoctrine()->getRepository(Building::class)->findOneBy(['admin' => $session->get('user')]);
            $logs = $this->getDoctrine()->getRepository(LogsHistory::class)->findBy(['building' => $building->getName()]);
        }

        $historyList = array();
        if ($logs) {
            foreach ($logs as $log) {
                $logInfo = array();

                $logInfo['visitor'] = $log->getVisitorName();
                $logInfo['building'] = $log->getBuilding();
                $logInfo['office'] = $log->getOfficeName();
                $logInfo['gate'] = '<b>Entrance Gate:</b> ' . $log->getGateCheckIn() . '<br><b>Exit Gate: </b>' . $log->getGateCheckOut();
                $logInfo['guard'] = '<b>Entrance Guard:</b> ' . $log->getGuardCheckIn() . '<br><b>Exit Guard: </b>' . $log->getGuardCheckOut();
                $logInfo['date'] = $log->getDateEntered() !== null ? $log->getDateEntered()->format('Y-m-d') : '<i>NULL</i>';
                $logInfo['time'] = '<b>Entrance Time: </b>' . ($log->getTimeEntered() !== null ? $log->getTimeEntered()->format('H:i') : '<i>NULL</i>')
                . '<br><b>Time Left From Office: </b>' . ($log->getTimeLeftFromOffice() === null ? '<i>NULL</i>' : $log->getTimeLeftFromOffice()->format('H:i'))
                . '<br><b>Exit Time: </b>' . ($log->getTimeExit() === null ? '<i>NULL</i>' : $log->getTimeExit()->format('H:i'));

                $historyList[] = $logInfo;
            }
        }

        return $this->json(array('data' => $historyList));
    }

}