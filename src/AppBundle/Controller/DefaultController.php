<?php

namespace AppBundle\Controller;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DefaultController extends Controller {

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

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function statusAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('AppBundle:Data')->getSensorStatus();

        $response = [];

        foreach($result as $item) {
            // Compare date
            $limitTime = date_modify(new DateTime('now'), '-1 hour')->format('H:i');
            $limitDate = date_modify(new DateTime('now'), '-1 hour')->format('d.m.Y');

            $response[] = [
                'id'   => $item['id'],
                'name' => $item['name'],
                'uuid' => $item['uuid'],
                'up'   => $item['date']->format('d.m.Y') >= $limitDate && $item['time']->format('H:i') >= $limitTime
            ];
        }

        return new JsonResponse($response);
    }
}
