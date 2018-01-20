<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 1/15/2018
 * Time: 12:29 AM
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class UProfileController extends Controller
{

    /**
     * @Route("/profile/{gmail}", name="uprofile")
     * @param Session $session
     * @param null $gmail
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewProfile(Session $session, $gmail = null)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        return $this->render('uprofile/uprofile.html.twig');

    }
}