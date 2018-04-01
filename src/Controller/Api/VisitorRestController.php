<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/31/2018
 * Time: 9:50 PM
 */

namespace App\Controller\Api;


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
        $data = json_decode($content, true);

        return $this->json(array(
            'success' => $data['firstName']
        ));
    }
}