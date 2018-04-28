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
use App\EntityClass\Firebase;
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
        $from = $request->request->get('from_userId');
        $to = $request->request->get('to_userId');

        $notificationBuilder = new Notification($title, $message, $this->getUserToken($to), $this->getParameter('firebase_server_key'), $from);
        $notification = $notificationBuilder->build();
        $headers = $notificationBuilder->getHeader();

        $firebase = new Firebase($this->getParameter('firebase_url'), $headers, $notification);

        if ($result = $firebase->sendNotification() === true)
            return $this->json(array(
                'success' => true,
                'message' => 'Notification sent successfully.'
            ), Response::HTTP_OK);

        return $this->json(array(
            'success' => false,
            'message' => $result
        ), Response::HTTP_NOT_FOUND);
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