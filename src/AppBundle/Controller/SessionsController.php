<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SessionsController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function newAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'sessions/login.html.twig', [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]
        );
    }
    
}
