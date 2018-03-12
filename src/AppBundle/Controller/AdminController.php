<?php

namespace AppBundle\Controller;

// Annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends BaseAdminController {

    /**
     * @Route("/", name="admin")
     * @Route("/", name="easyadmin")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function indexAction(Request $request) {
        $this->initialize($request);

        if (null === $request->query->get('entity')) {
            return $this->redirectToBackendHomepage();
        }

        $action = $request->query->get('action', 'list');
        if (!$this->isActionAllowed($action)) {
            throw new ForbiddenActionException(array('action' => $action, 'entity_name' => $this->entity['name']));
        }

        return $this->executeDynamicMethod($action.'<EntityName>Action');
    }

    /**
     * @Route("/dashboard", name="admin_dashboard")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return Response
     */
    public function dashboardAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dataCount = $em->getRepository('AppBundle:Data')->getDataCount();
        $sensorCount = $em->getRepository('AppBundle:Sensor')->getSensorCount();
        $data = $em->getRepository('AppBundle:Data')->getRecentTemps(10);

        return $this->render('AppBundle:EasyAdmin:dashboard.html.twig', [
            'dataCount' => $dataCount[0]['amount'],
            'sensorCount' => $sensorCount[0]['amount'],
            'data' => $data
        ]);
    }

    /**
     * @Route("/export", name="admin_export")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return StreamedResponse
     */
    public function exportAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('AppBundle:Data')->getMax(50);


        $response = new StreamedResponse();
        $response->setCallback(function() {
            $handle = fopen('php://output', 'w+');

            // Add the header of the CSV file
            fputcsv($handle, array('Name', 'Surname', 'Age', 'Sex'),';');
            // Query data from database
            $results = $this->connection->query("Replace this with your query");
            // Add the data queried from database
            while($row = $results->fetch()) {
                fputcsv(
                    $handle, // The file pointer
                    array($row['name'], $row['surname'], $row['age'], $row['sex']), // The fields
                    ';' // The delimiter
                );
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

        return $response;
    }

}