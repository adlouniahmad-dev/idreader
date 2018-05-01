<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/26/2018
 * Time: 4:37 PM
 */

namespace App\Controller\Api;


use App\Entity\NotificationSettings;
use App\Entity\Office;
use App\Entity\OfficeSettings;
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

            $roles = $this->getUserRoles($user);
            if (in_array('Premise Owner', $roles)) {

                $office = $this->getDoctrine()->getRepository(Office::class)->findOneBy(['user' => $user]);
                $settings = $this->getDoctrine()->getRepository(OfficeSettings::class)->findOneBy(['office' => $office]);

                if (!$settings) {
                    $officeSetting = new OfficeSettings();
                    $officeSetting->setOffice($office);
                    $entityManager->persist($officeSetting);
                    $entityManager->flush();
                }

                $notification = $entityManager->getRepository(NotificationSettings::class)->findOneBy(['user' => $user]);
                if (!$notification) {
                    $notificationSettings = new NotificationSettings();
                    $notificationSettings->setUser($user);
                    $entityManager->persist($notificationSettings);
                    $entityManager->flush();
                }
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

    /**
     * @param User $user
     * @return array
     */
    private function getUserRoles(User $user): array
    {
        $roles = $user->getRoles();
        $rolesArray = array();
        foreach ($roles as $value) {
            $rolesArray[] = $this->getRoleName($value);
        }
        return $rolesArray;
    }

    /**
     * @param $role
     * @return string
     */
    private function getRoleName($role): string
    {
        if ($role->getRoleName() == 'fowner')
            return 'Facility Owner';
        else if ($role->getRoleName() == 'fadmin')
            return 'Facility Administrator';
        else if ($role->getRoleName() == 'powner')
            return 'Premise Owner';
        else
            return 'Security Guard';
    }
}