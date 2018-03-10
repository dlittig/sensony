<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class StartController extends Controller {

    /**
     * @Get("/", name="start")
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, AuthenticationUtils $authUtils) {
        return $this->render('start/index.html.twig', [
            'error' => null,
            'last_username' => null
        ]);
    }
}
