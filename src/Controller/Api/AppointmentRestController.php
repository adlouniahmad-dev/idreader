<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/26/2018
 * Time: 5:49 PM
 */

namespace App\Controller\Api;


use App\Entity\Appointment;
use App\Entity\NotificationSettings;
use App\Entity\Office;
use App\Entity\Token;
use App\Entity\User;
use App\EntityClass\Firebase;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as FRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\EntityClass\Notification;

/**
 * Class AppointmentRestController
 * @package App\Controller\Api
 * @FRoute("/api/appointments")
 */
class AppointmentRestController extends Controller
{

    /**
     * @Route("/check", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function checkIfAppointment(Request $request)
    {
        if ($request->request->get('ssn') === null || $request->request->get('from_userId') === null)
            return $this->json(array(
                'success' => false,
                'message' => 'Not all variables are defined.'
            ), Response::HTTP_NOT_FOUND);

        $ssn = $request->request->get('ssn');
        $from = $request->request->get('from_userId');

        try {
            $appointment = $this->getDoctrine()->getRepository(Appointment::class)->findBySsnAndTodayDay($ssn);
            $officeName = $appointment->getOffice();

            $office = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['officeNb' => $officeName]);
            $premiseOwner = $office->getUser();
            $notificationSettings = $this->getDoctrine()->getRepository(NotificationSettings::class)->findOneBy(['user' => $premiseOwner]);

            if ($notificationSettings->getEnabled() === false)
                return $this->json(array(
                    'success' => false,
                    'notificationSettings' => false,
                    'message' => 'Notifications are disabled.'
                ));

            $timeOfAppointment = $appointment->getTime();

            date_default_timezone_set("Asia/Beirut");
            $timeNow = new \DateTime();

            $interval = $timeNow->diff($timeOfAppointment);
            $interval->format('h:m:s');




            // some process will be here (not done yet)
            $flag = true;

            if (!$flag) {

                $securityGuard = $this->getDoctrine()->getRepository(User::class)->find($userId);
                $securityToken = $this->getDoctrine()->getRepository(Token::class)->findOneBy(['user' => $securityGuard]);

                $title = 'Allow Entering?';
                $message = 'Allow ' . $appointment->getApplicantName() . ' to enter the meeting?';

                $notificationBuilder = new Notification($title, $message, $securityToken, $this->getParameter('firebase_server_key'), $from);
                $notification = $notificationBuilder->build();
                $headers = $notificationBuilder->getHeader();

                $firebase = new Firebase($this->getParameter('firebase_url'), $headers, $notification);
                if ($result = $firebase->sendNotification() === true)
                    return $this->json(array(
                        'success' => true,
                        'notification' => true,
                        'hasAppointment' => true,
                        'inTime' => $flag,
                        'message' => 'Notification sent successfully.'
                    ));

                return $this->json(array(
                    'success' => true,
                    'notification' => false,
                    'hasAppointment' => true,
                    'inTime' => $flag,
                    'message' => 'Error in sending notification. Please try again.'
                ));
            }

            return $this->json(array(
                'success' => true,
                'hasAppointment' => true,
                'inTime' => $flag,
                'message' => $flag ? 'Can access.' : 'Can\'t access.'
            ));


        } catch (NoResultException | NonUniqueResultException $e) {

            return $this->json(array(
                'success' => true,
                'hasAppointment' => false,
                'inTime' => false,
                'message' => 'Visitor has no appointment'
            ));
        }

    }

}