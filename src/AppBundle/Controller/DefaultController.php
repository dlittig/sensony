<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

const EXPIRED_MESSAGE = 'Your account has expired. Please get in contact with the administrator to reactivate your account.';

class DefaultController extends Controller {

    /**
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, AuthenticationUtils $authUtils) {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $expired = false;

        if($user instanceof  User) {
            if ($user->getTimeToLive() !== null) {
                $expired = $user->getTimeToLive() < new \DateTime('now');
            }
        }

        if($expired === true) {
            $providerKey = $this->container->getParameter('main');
            $token = new AnonymousToken($providerKey, 'anon.');
            $this->get('security.context')->setToken($token);
            $this->get('request')->getSession()->invalidate();
        }

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER') || $expired === true) {
            // get the login error if there is one
            $error = $authUtils->getLastAuthenticationError();
            //$error = var_dump($this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findAll());

            // last username entered by the user
            $lastUsername = $authUtils->getLastUsername();

            return $this->render('AppBundle:Start:index.html.twig', [
                'error' => ($expired !== true) ? $error : EXPIRED_MESSAGE,
                'last_username' => $lastUsername
            ]);
        } else {
            return $this->redirectToRoute('admin');
        }
    }
}
