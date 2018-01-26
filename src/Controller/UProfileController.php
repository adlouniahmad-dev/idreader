<?php
/**
 * Created by PhpStorm.
 * UserForm: Ahmad Adlouni
 * Date: 1/15/2018
 * Time: 12:29 AM
 */

namespace App\Controller;


use App\Entity\User;
use App\Form\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class UProfileController extends Controller
{

    /**
     * @Route("/profile/{account}", name="uprofile")
     * @param Session $session
     * @param $account
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function viewProfile(Session $session, $account)
    {
        if (!$session->has('gmail'))
            return $this->redirectToRoute('login');

        $gmail = $account . '@gmail.com';
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['gmail' => $gmail]);

        if ($user) {
            $userRoles = $this->getDoctrine()->getRepository(User::class)->getUserRoles($user->getId());

            $userForm = new User();
            $form = $this->createForm(UserType::class, $userForm);

            return $this->render('uprofile/uprofile.html.twig', array(
                'user' => $user,
                'userRoles' => $userRoles,
                'form' => $form->createView()
            ));
        }

        die('UserForm not found');
    }

}