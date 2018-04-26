<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/26/2018
 * Time: 4:37 PM
 */

namespace App\Controller\Api;


use App\Entity\Token;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as FRoute;

/**
 * Class MemberRestController
 * @package App\Controller\Api
 * @FRoute("/api/token")
 */
class TokenRestController extends Controller
{

    /**
     * @Route("/register", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function registerToken(Request $request)
    {
        if ($request->request->get('userId') === null && $request->request->get('token') === null) {
            return $this->json(array(
                'success' => false,
                'message' => 'User Id or token are null',
            ), Response::HTTP_NOT_FOUND);
        }

        $userId = $request->request->get('userId');
        $receivedToken = $request->request->get('token');

        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if (!$user)
            return $this->json(array(
                'success' => false,
                'message' => 'User not found.'
            ), Response::HTTP_NOT_FOUND);

        $token = $this->getDoctrine()->getRepository(Token::class)->findOneBy(['user' => $user]);
        $entityManager = $this->getDoctrine()->getManager();

        try {

            if (!$token) {
                $newToken = new Token();
                $newToken->setUser($user);
                $newToken->setToken($receivedToken);
                $entityManager->persist($newToken);
                $entityManager->flush();
            } else if ($token !== $receivedToken) {
                $token->setToken($receivedToken);
                $entityManager->flush();
            }

            return $this->json(array(
                'success' => true,
                'message' => 'Token added successfully.'
            ), Response::HTTP_OK);

        } catch (\Exception $e) {

            return $this->json(array(
                'success' => false,
                'message' => 'Error adding the token'
            ), Response::HTTP_NOT_FOUND);
        }

    }
}