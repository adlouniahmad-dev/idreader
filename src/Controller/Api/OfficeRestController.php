<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/1/2018
 * Time: 9:09 PM
 */

namespace App\Controller\Api;


use App\Entity\Office;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfficeRestController extends Controller
{

    /**
     * @Route("/api/office/getOffices/{gmail}", methods={"GET"})
     * @param $gmail
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOffices($gmail)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['gmail' => $gmail]);
        if (!$user)
            return $this->json(array(
                'success' => false,
                'message' => 'User not found',
            ), Response::HTTP_NOT_FOUND);

        $buildings = $this->getUserBuildings($user);
        if (!$buildings)
            return $this->json(array(
                'success' => false,
                'message' => 'Building not found.',
            ), Response::HTTP_NOT_FOUND);

        $offices = $this->getDoctrine()->getRepository(Office::class)->findBy(['building' => $buildings[0]]);
        if (!$offices)
            return $this->json(array(
                'success' => false,
                'message' => 'There is no offices right now.',
            ), Response::HTTP_NOT_FOUND);

        $officesArray = array();
        foreach ($offices as $office) {
            $officeInfo = array();

            if ($office->getUser() === null)
                continue;

            $officeInfo['id'] = $office->getId();
            $officeInfo['officeNb'] = $office->getOfficeNb();
            $officeInfo['floorNb'] = $office->getFloorNb();
            $officeInfo['premiseOwner'] = $office->getUser()->getFullName();

            $officesArray[] = $officeInfo;
        }

        $data = array();
        $data['success'] = true;
        $data['message'] = 'Offices found.';
        $data['offices'] = $officesArray;

        return $this->json($data, Response::HTTP_OK);

    }

    /**
     * @param User $user
     * @return array
     */
    private function getUserBuildings(User $user): array
    {
        $buildings = $user->getBuildings();
        $buildingsArray = array();
        foreach ($buildings as $building)
            $buildingsArray[] = $building;

        return $buildingsArray;
    }

}