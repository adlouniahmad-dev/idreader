<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class LoginController extends Controller
{

    /**
     * @Route("/", name="index")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Session $session)
    {
        if ($session->has('gmail'))
            return $this->redirectToRoute('dashboard');
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/login", name="login")
     * @param Session $session
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function login(Session $session)
    {
        if (!$session->has("gmail")) {
            $client = $this->create_client();
            $client->addScope(\Google_Service_Oauth2::USERINFO_PROFILE);
            $client->addScope(\Google_Service_Oauth2::USERINFO_EMAIL);

            $url = $client->createAuthUrl();

            return $this->render("login/login.html.twig", array(
                "url" => $url
            ));
        }

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/login/check-user")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function check_user(Request $request)
    {
        $client = $this->create_client();
        $service = new \Google_Service_Oauth2($client);

        if ($request->query->get('code')) {
            $code = $request->query->get('code');

            $client->authenticate($code);
            $client->setAccessToken($client->getAccessToken());

            $userDetails = $service->userinfo_v2_me->get();
            $email = $userDetails->email;

            if ($user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['gmail' => $email])) {

                $session = new Session();

                $session->set('given_name', $user->getGivenName());
                $session->set('family_name', $user->getFamilyName());
                $session->set('gmail', $user->getGmail());
                $session->set('dob', $user->getDob());
                $session->set('phone_nb', $user->getPhoneNb());
                $session->set('date_created', $user->getDateCreated());

                $session->set('image', $userDetails->picture);
                $session->set('token', $client->getAccessToken());

                return $this->redirectToRoute('dashboard');
            } else {
                $client->revokeToken(['refresh_token' => $client->getAccessToken()]);
                return $this->redirectToRoute('login');
            }
        }

        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/logout", name="logout")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logout(Session $session)
    {
        if ($session->has('gmail')) {
            $client = $this->create_client();
            $token = $session->get('token');
            $client->revokeToken(['refresh_token' => $token]);
            $session->clear();
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @return \Google_Client
     */
    private function create_client()
    {
        $client = new \Google_Client();
        $client->setClientId($this->getParameter('google_client_id'));
        $client->setClientSecret($this->getParameter('google_client_secret'));
        $client->setRedirectUri($this->getParameter('google_redirect'));
        $client->setHostedDomain($this->getParameter('google_hosted_domain'));
        return $client;
    }


}
