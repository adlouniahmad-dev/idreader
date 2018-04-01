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

class GuardRestController extends Controller
{

    /**
     * @Route("/api/guard/check/{gmail}/{macAddress}", methods={"GET"})
     * @param $gmail
     * @param $macAddress
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function checkGuard($gmail, $macAddress)
    {

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['gmail' => $gmail]);
        if (!$user)
            return $this->json(array(
                'success' => 'no',
                'response' => Response::HTTP_NOT_FOUND
            ));

        $guard = $this->getDoctrine()->getRepository(Guard::class)->findOneBy(['user' => $user]);
        if (!$guard)
            return $this->json(array(
                'success' => 'no',
                'response' => Response::HTTP_NOT_FOUND
            ));

        if (strtolower($guard->getDevice()->getMacAddress()) !== strtolower($macAddress))
            return $this->json(array(
                'success' => 'no',
                'response' => Response::HTTP_NOT_FOUND
            ));

        return $this->json(array(
            'success' => 'yes',
            'response' => Response::HTTP_OK
        ));
    }

}