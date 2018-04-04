<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/29/2018
 * Time: 4:46 PM
 */

namespace App\Controller;


use App\Entity\Blacklist;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageBlacklistController extends Controller
{

    /**
     * @Route("/blacklist", name="viewBlacklist")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewBlacklist(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) && !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');


        return $this->render('blacklist/blacklist.html.twig');
    }

    /**
     * @Route("/api/getBlacklist/{page}/{query}", methods={"GET"})
     * @Route("/api/getBlacklist/{page}", methods={"GET"})
     * @param int $page
     * @param string $query
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTheBlacklist($page = 1, $query = '')
    {

        $currentPage = $page;

        $repo = $this->getDoctrine()->getRepository(Blacklist::class);
        $visitors = $repo->getAllBlacklistedVisitors($currentPage, $query);

        $totalVisitors = $visitors->count();
        $totalVisitorsReturned = $visitors->getIterator()->count();
        $limit = 10;
        $maxPages = ceil($totalVisitors / $limit);

        $data = array();
        $data['totalVisitors'] = $totalVisitors;
        $data['totalVisitorsReturned'] = $totalVisitorsReturned;
        $data['limit'] = $limit;
        $data['currentPage'] = (int)$currentPage;
        $data['maxPages'] = $maxPages;

        $visitorsArray = array();

        foreach ($visitors as $visitor) {
            $visitorInfo = array();
            $visitorInfo['id'] = $visitor->getId();
            $visitorInfo['dateAddedToBlacklist'] = date_format($visitor->getDateAdded(), 'jS F, Y');

            $visitorInfo['visitor'] = array();
            $visitorInfo['visitor']['id'] = $visitor->getVisitor()->getId();
            $visitorInfo['visitor']['firstName'] = $visitor->getVisitor()->getFirstName();
            $visitorInfo['visitor']['middleName'] = $visitor->getVisitor()->getMiddleName();
            $visitorInfo['visitor']['lastName'] = $visitor->getVisitor()->getLastName();
            $visitorInfo['visitor']['nationality'] = $visitor->getVisitor()->getCountry();

            $visitorsArray[] = $visitorInfo;
        }

        $data['blacklist'] = $visitorsArray;

        return $this->json($data);
    }
}