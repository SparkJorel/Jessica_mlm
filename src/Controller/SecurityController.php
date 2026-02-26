<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="jtwc_app_login", schemes={"http","https"})
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/", name="jtwc_app_redirect", schemes={"http","https"})
     * @return Response
     */
    public function redirectTo(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('jtwc_app_login');
        } else {
            return $this->redirectToRoute('genealogy_tree');
        }
    }

    /**
     * @Route("/logout", name="jtwc_app_logout")
     */
    public function logout()
    {
    }
}
