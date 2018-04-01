<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/31/2018
 * Time: 9:50 PM
 */

namespace App\Controller\Api;


use App\Entity\Visitor;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\DatabaseObjectExistsException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VisitorRestController extends Controller
{

    /**
     * @Route("/api/visitor/add", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function addVisitor(Request $request)
    {
        $content = $request->getContent();
        if (!$content)
            return $this->json(array(
                'success' => 'no',
                'message' => 'Body is empty.',
                'response' => Response::HTTP_NOT_FOUND,
            ));

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
            $visitor->setNationality($data['nationality']);

            $entityManager->persist($visitor);
            $entityManager->flush();

        } catch (\Exception $e) {

            return $this->json(array(
                'success' => 'no',
                'message' => $e->getMessage(),
                'response' => Response::HTTP_NOT_FOUND
            ));
        }

        return $this->json(array(
            'success' => 'yes',
            'message' => 'Visitor added successfully.',
            'response' => Response::HTTP_OK,
        ));
    }
}