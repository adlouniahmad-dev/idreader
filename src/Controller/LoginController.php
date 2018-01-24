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
     * @Route("/login", name="login")
     * @Route("/login/error", name="error-login")
     * @param Session $session
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function login(Session $session, Request $request)
    {
        if (!$session->has("gmail")) {
            $client = $this->create_client();
            $client->addScope(\Google_Service_Oauth2::USERINFO_PROFILE);
            $client->addScope(\Google_Service_Oauth2::USERINFO_EMAIL);

            $url = $client->createAuthUrl();
            $currentRoute = $request->attributes->get('_route');

            if ($currentRoute == 'error-login') {
                return $this->render('login/login.html.twig', array(
                    'error' => 'error',
                    'url' => $client->createAuthUrl()
                ));
            }
            else {
                return $this->render("login/login.html.twig", array(
                    "url" => $url
                ));
            }
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

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['gmail' => $email]);

            if ($user) {
                if ($user->getImageUrl() != $userDetails->picture) {
                    $user->setImageUrl($userDetails->picture);
                    $em->flush();
                }

                $user_id = $user->getId();
                $user = $this->getDoctrine()->getRepository(User::class)->getUser($user_id);

                if ($user) {

                    $roles = $user[0]->getRoles();
                    $session = new Session();
                    $session->set('given_name', $user[0]->getGivenName());
                    $session->set('gmail', $user[0]->getGmail());
                    $session->set('roles', $roles->count());
                    $session->set('image', $userDetails->picture);
                    $session->set('token', $client->getAccessToken());

//                    print_r($roles->count());
//                    return $this->render('dashboard/dashboard.html.twig');
                    return $this->redirectToRoute('dashboard');
                }

            } else {
                return new Response(
                    '<script type="text/javascript">document.location.href = "https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=http://localhost:8000/login/error"</script>'
                );
            }
        }

        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/logout/{slug}", name="logout")
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
