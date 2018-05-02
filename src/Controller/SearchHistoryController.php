<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/19/2018
 * Time: 5:36 PM
 */

namespace App\Controller;


use App\Entity\LogsSearchHistory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class SearchHistoryController extends Controller
{

    /**
     * @Route("/search/history", name="searchHistory")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewSearchHistory(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');

        return $this->render('searchHistory/searchHistory.html.twig');
    }

    /**
     * @Route("/api/search/history/get", methods={"GET"})
     */
    public function getHistory()
    {
        $searchHistory = $this->getDoctrine()->getRepository(LogsSearchHistory::class)->findAll();

        $historyList = array();
        if ($searchHistory) {
            foreach ($searchHistory as $history) {
                $historyInfo = array();
                $historyInfo['member'] = '<b>Name:</b> ' . $history->getUser() . '<br><b>Role(s):</b><br>' . $history->getRole();
                $historyInfo['visitorName'] = $history->getVisitorName();
                $historyInfo['building'] = $history->getBuilding();
                $historyInfo['office'] = $history->getOffice();
                $historyInfo['gate'] = '<b>Entrance Gate:</b> ' . $history->getEntranceGate() . '<br><b>Exit Gate:</b> ' . $history->getExitGate();
                $historyInfo['guard'] = '<b>Entrance Guard:</b> ' . $history->getEntranceGuard() . '<br><b>Exit Guard:</b> ' . $history->getExitGuard();
                $historyInfo['date'] = '<b>From:</b> ' . $history->getDateFrom() . '<br><b>To:</b> ' . $history->getDateTo();
                $historyInfo['timeEntered'] = '<b>From:</b> ' . $history->getTimeEnteredFrom() . '<br><b>To:</b> ' . $history->getTimeEnteredTo();
                $historyInfo['timeLeft'] = '<b>From:</b> ' . $history->getTimeExitFrom() . '<br><b>To:</b> ' . $history->getTimeExitTo();
                $historyInfo['timeLeftFromOffice'] = '<b>From:</b> ' . $history->getTimeLeftFromOfficeFrom() . '<br><b>To:</b> ' . $history->getTimeLeftFromOfficeTo();
                $historyInfo['dateOfQuery'] = $history->getDateTimeSearch()->format('jS F, Y, g:i a');

                array_push($historyList, $historyInfo);
            }
        }

        return $this->json(array('data' => $historyList));

    }
}