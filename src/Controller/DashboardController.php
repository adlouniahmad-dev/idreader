<?php
/**
 * Created by PhpStorm.
 * UserForm: Ahmad Adlouni
 * Date: 1/14/2018
 * Time: 9:16 PM
 */

namespace App\Controller;


use App\Entity\Building;
use App\Entity\LogGate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function dashboard(Session $session)
    {
        if (!$session->has("gmail"))
            return $this->redirectToRoute('login');

        $buildingsOptions = '';
        if (in_array('fowner', $session->get('roles'))) {
            $buildings = $this->getDoctrine()->getRepository(Building::class)->findAll();
            foreach ($buildings as $building) {
                $buildingsOptions .= '<option value=' . $building->getId() . '>' . $building->getName() . '</option>';
            }
        } else if (in_array('fadmin', $session->get('roles'))) {
            $building = $this->getDoctrine()->getRepository(Building::class)->findOneBy(['admin' => $session->get('user')->getId()]);
            $buildingsOptions .= '<option value=' . $building->getId() . '>' . $building->getName() . '</option>';
        }

        return $this->render('dashboard/dashboard.html.twig', array(
            'buildings' => $buildingsOptions
        ));

    }

    /**
     * @Route("/api/getScansPerGate/{buildingId}/{date}")
     * @param $buildingId
     * @param $date
     * @return JsonResponse|Response
     * @throws \Doctrine\DBAL\DBALException
     * @Method("GET")
     */
    public function getScansPerGate($buildingId, $date)
    {
        $result = $this->getDoctrine()->getRepository(LogGate::class)->findByDate($buildingId, $date);
        return new JsonResponse($result);
    }

    /**
     * @Route("/api/getScansPerDayPerMonth/{buildingId}/{month}/{year}")
     * @param $buildingId
     * @param $month
     * @param $year
     * @return JsonResponse
     * @throws \Doctrine\DBAL\DBALException
     * @Method("GET")
     */
    public function getScansPerDayPerMonth($buildingId, $month, $year)
    {
        $result = $this->getDoctrine()->getRepository(LogGate::class)->findByMonth($buildingId, $month, $year);
        return new JsonResponse($result);
    }

}