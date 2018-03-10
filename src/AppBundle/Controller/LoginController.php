<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller {

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Post("/", name="login")
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils) {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            // get the login error if there is one
            $error = $authUtils->getLastAuthenticationError();

            // last username entered by the user
            $lastUsername = $authUtils->getLastUsername();

            return $this->redirectToRoute('start', [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]);
        } else {
            return $this->redirectToRoute('easy_admin_bundle');
        }
    }
}
