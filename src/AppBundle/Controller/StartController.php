<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class StartController extends Controller {

    /**
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, AuthenticationUtils $authUtils) {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

            // get the login error if there is one
            $error = $authUtils->getLastAuthenticationError();

            // last username entered by the user
            $lastUsername = $authUtils->getLastUsername();

            return $this->render('AppBundle:Start:index.html.twig', [
                'error' => $error,
                'last_username' => $lastUsername
            ]);
        } else {
            return $this->redirectToRoute('admin');
        }
    }
}
