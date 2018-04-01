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
     * @Route("/api/getBlacklist/{query}", methods={"GET"})
     * @param string $query
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTheBlacklist($query = '')
    {

        $repo = $this->getDoctrine()->getRepository(Blacklist::class);
        $visitors = $repo->getAllBlacklistedVisitors($query);

        $totalVisitors = count($visitors);

        $data = array();
        $data['totalVisitors'] = $totalVisitors;

        $visitorsArray = array();

        for ($i = 0; $i < count($visitors); $i++) {
            $visitorInfo = array();
            $visitorInfo['id'] = $visitors[$i]->getId();
            $visitorInfo['dateAddedToBlacklist'] = date_format($visitors[$i]->getDateAdded(), 'jS F, Y');

            $visitorInfo['visitor'] = array();
            $visitorInfo['visitor']['id'] = $visitors[$i]->getVisitor()->getId();
            $visitorInfo['visitor']['firstName'] = $visitors[$i]->getVisitor()->getFirstName();
            $visitorInfo['visitor']['middleName'] = $visitors[$i]->getVisitor()->getMiddleName();
            $visitorInfo['visitor']['lastName'] = $visitors[$i]->getVisitor()->getLastName();
            $visitorInfo['visitor']['nationality'] = $visitors[$i]->getVisitor()->getCountry();

            $visitorsArray[] = $visitorInfo;
        }

        $data['blacklist'] = $visitorsArray;

        return $this->json($data);
    }
}