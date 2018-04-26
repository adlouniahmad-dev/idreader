<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/26/2018
 * Time: 5:49 PM
 */

namespace App\Controller\Api;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as FRoute;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AppointmentRestController
 * @package App\Controller\Api
 * @FRoute("/api/appointments")
 */
class AppointmentRestController extends Controller
{

    /**
     * @Route("/check", methods={"POST"})
     */
    public function checkIfAppointment()
    {

    }

}