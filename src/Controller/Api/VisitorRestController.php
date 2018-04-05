<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/31/2018
 * Time: 9:50 PM
 */

namespace App\Controller\Api;


use App\Country;
use App\Entity\Log;
use App\Entity\Office;
use App\Entity\Visitor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as FRoute;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class VisitorRestController
 * @package App\Controller\Api
 * @FRoute("/api/visitor")
 */
class VisitorRestController extends Controller
{

    /**
     * @Route("/add", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function addVisitor(Request $request)
    {
        $content = $request->getContent();
        if (!$content)
            return $this->json(array(
                'success' => false,
                'message' => 'Body is empty.',
            ), Response::HTTP_NOT_FOUND);

        $data = json_decode($content, true);
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $visitor = new Visitor();
            $visitor->setFirstName($data['firstName']);
            $visitor->setMiddleName($data['middleName']);
            $visitor->setLastName($data['lastName']);
            $visitor->setDateCreated(new \DateTime());
            $visitor->setDocumentType($data['documentType']);
            $visitor->setHasCard($data['documentType'] === 'ID Card' ? 1 : 0);
            $visitor->setSsn($data['ssn']);

            $country = new Country();
            $visitor->setNationality($country->getCountryCode($data['nationality']));

            $entityManager->persist($visitor);
            $entityManager->flush();


            $log = new Log();
            $log->setVisitor($visitor);
            $log->setTimeEntered(new \DateTime());
            $log->setDateCreated(new \DateTime());
            $log->setIsSuspicious(0);

            $office = $this->getDoctrine()->getRepository(Office::class)->find($data['officeId']);
            $log->setOffice($office);

            $entityManager->persist($log);
            $entityManager->flush();

        } catch (\Exception $e) {

            return $this->json(array(
                'success' => false,
                'message' => $e->getMessage(),
            ), Response::HTTP_NOT_FOUND);
        }


        return $this->json(array(
            'success' => true,
            'message' => 'Visitor added successfully.',
        ), Response::HTTP_OK);
    }
}