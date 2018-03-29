<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 3/29/2018
 * Time: 4:46 PM
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageBlacklistController extends Controller
{

    /**
     * @Route("/blacklist", name="viewBlacklist")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewBlacklist(Session $session)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        if (!in_array('fowner', $session->get('roles')) || !in_array('fadmin', $session->get('roles')))
            return $this->render('errors/access_denied.html.twig');



        return $this->render('blacklist/blacklist.html.twig');
    }
}