<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/26/2018
 * Time: 7:22 PM
 */

namespace App\Controller\Api;


use App\Entity\Guard;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as FRoute;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MemberRestController
 * @package App\Controller\Api
 * @FRoute("/api/user")
 */
class MemberRestController extends Controller
{

    /**
     * @Route("/check/{gmail}", methods={"GET"})
     * @Route("/check/{gmail}/{macAddress}", methods={"GET"})
     * @param $gmail
     * @param null $macAddress
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function check($gmail, $macAddress = null)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['gmail' => $gmail]);
        if (!$user)
            return $this->json(array(
                'success' => false,
                'message' => 'User not found.'
            ), Response::HTTP_NOT_FOUND);

        $response = array();

        if ($macAddress) {
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

            $response['guardId'] = $guard->getId();
        }

        $response['success'] = true;
        $response['userId'] = $user->getId();
        $response['message'] = 'Logged in successfully.';

        return $this->json($response, Response::HTTP_OK);
    }

}