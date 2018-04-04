<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/31/2018
 * Time: 9:02 PM
 */

namespace App\Controller\Api;


use App\Entity\Guard;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as FRoute;

/**
 * Class GuardRestController
 * @package App\Controller\Api
 * @FRoute("/api/guard")
 */
class GuardRestController extends Controller
{

    /**
     * @Route("/check/{gmail}/{macAddress}", methods={"GET"})
     * @param $gmail
     * @param $macAddress
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function checkLogin($gmail, $macAddress)
    {

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['gmail' => $gmail]);
        if (!$user)
            return $this->json(array(
                'success' => false,
                'message' => 'Facility member not found.'
            ), Response::HTTP_NOT_FOUND);

        $guard = $this->getDoctrine()->getRepository(Guard::class)->findOneBy(['user' => $user]);
        if (!$guard)
            return $this->json(array(
                'success' => false,
                'message' => 'Guard not found.'
            ), Response::HTTP_NOT_FOUND);

        if (strtolower($guard->getDevice()->getMacAddress()) !== strtolower($macAddress))
            return $this->json(array(
                'success' => false,
                'message' => 'MAC Address Error.'
            ), Response::HTTP_NOT_FOUND);

        return $this->json(array(
            'success' => true,
            'message' => 'Can be logged in.'
        ), Response::HTTP_OK);
    }

}