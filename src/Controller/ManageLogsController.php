<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/7/2018
 * Time: 1:43 PM
 */

namespace App\Controller;


use App\Entity\Log;
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

}