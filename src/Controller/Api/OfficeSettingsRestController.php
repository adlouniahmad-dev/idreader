<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 5/1/2018
 * Time: 5:43 PM
 */

namespace App\Controller\Api;


use App\Entity\NotificationSettings;
use App\Entity\Office;
use App\Entity\OfficeSettings;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as FRoute;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OfficeSettingsRestController
 * @package App\Controller\Api
 * @FRoute("/api/office/settings")
 */
class OfficeSettingsRestController extends Controller
{

    /**
     * @Route("/set", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function setSettings(Request $request)
    {
        if ($request->request->get('enabled') === null ||
            $request->request->get('walk') === null ||
            $request->request->get('service') === null ||
            $request->request->get('userId') === null ||
            $request->request->get('late') === null)
            return $this->json(array(
                'success' => false,
                'message' => 'Some fields are null'
            ), Response::HTTP_NOT_FOUND);

        $userId = $request->request->get('userId');
        $enabled = $request->request->get('enabled');
        $late = $request->request->get('late');
        $walk = $request->request->get('walk');
        $service = $request->request->get('service');

        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
        $office = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['user' => $user]);

        $entityManager = $this->getDoctrine()->getManager();
        $officeSettings = $entityManager->getRepository(OfficeSettings::class)->findOneBy(['office' => $office]);
        $notificationSettings = $entityManager->getRepository(NotificationSettings::class)->findOneBy(['user' => $user]);

        $officeSettings->setAverageWaitingTime((int)$service);
        $officeSettings->setWalkTime((int)$walk);
        $entityManager->flush();

        $notificationSettings->setEnabled($enabled == 'true' ? true : false);
        $notificationSettings->setLateAfter((int)$late);
        $entityManager->flush();

        return $this->json(array(
            'success' => true,
            'message' => 'Settings updated successfully.'
        ), Response::HTTP_OK);
    }

    /**
     * @Route("/get/{userId}", methods={"GET"})
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSettings($userId)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
        if (!$user)
            return $this->json(array(
                'success' => false,
                'message' => 'User not found'
            ), Response::HTTP_NOT_FOUND);

        $settings = array();

        $notificationSettings = $this->getDoctrine()->getRepository(NotificationSettings::class)->findOneBy(['user' => $user]);
        $settings['enabled'] = $notificationSettings->isEnabled();
        $settings['late'] = $notificationSettings->getLateAfter();

        $office = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['user' => $user]);
        $officeSettings = $this->getDoctrine()->getRepository(OfficeSettings::class)->findOneBy(['office' => $office]);
        $settings['service'] = $officeSettings->getAverageWaitingTime();
        $settings['walk'] = $officeSettings->getWalkTime();

        return $this->json(array(
            'success' => true,
            'settings' => $settings,
            'message' => 'Setting received successfully.'
        ), Response::HTTP_OK);

    }

}