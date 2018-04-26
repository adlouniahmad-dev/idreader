<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/26/2018
 * Time: 4:57 PM
 */

namespace App\Controller\Api;


use App\Entity\Token;
use App\Entity\User;
use App\EntityClass\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as FRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationRestController
 * @package App\Controller\Api
 * @FRoute("/api/notification")
 */
class NotificationRestController extends Controller
{

    /**
     * @Route("/send", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function sendNotification(Request $request)
    {
        $title = $request->request->get('title');
        $message = $request->request->get('message');
        $userId = $request->request->get('userId');

        $notificationBuilder = new Notification($title, $message, $this->getUserToken($userId), $this->getParameter('firebase_server_key'));
        $notification = $notificationBuilder->build();
        $headers = $notificationBuilder->getHeader();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getParameter('firebase_url'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));

        if (!curl_exec($ch)) {
            return $this->json(array(
                'success' => false,
                'message' => curl_error($ch)
            ), Response::HTTP_NOT_FOUND);
        }

        curl_close($ch);

        return $this->json(array(
            'success' => true,
            'message' => 'Notification sent successfully.'
        ), Response::HTTP_OK);
    }

    /**
     * @param $userId
     * @return Token|null|object
     */
    private function getUserToken($userId)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
        $token = $this->getDoctrine()->getRepository(Token::class)->findOneBy(['user' => $user]);
        return $token;
    }

}