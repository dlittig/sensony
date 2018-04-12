<?php

namespace AppBundle\Controller;

// Annotations
use Psr\Log\LoggerInterface;
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
        $recentTimestamp = $em->getRepository('AppBundle:Data')->getRecentDateTime();
        $sensorCount = $em->getRepository('AppBundle:Sensor')->getSensorCount();
        $data = $em->getRepository('AppBundle:Data')->getRecent(['temp', 'pressure'], 10);

        $recentTimestamp = (count($recentTimestamp) > 0) ? $recentTimestamp : null;

        return $this->render('AppBundle:EasyAdmin:dashboard.html.twig', [
            'dataCount' => $dataCount[0]['amount'],
            'sensorCount' => $sensorCount[0]['amount'],
            'data' => $data,
            'recentTimestamp' => $recentTimestamp[0]
        ]);
    }

    /**
     * @Route("/export", name="admin_export")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return StreamedResponse
     */
    public function exportAction(Request $request, LoggerInterface $logger) {
        $response = new StreamedResponse();
        $response->setCallback(function() use ($request, $logger) {
            $handle = fopen('php://output', 'w+');

            $em = $this->getDoctrine()->getManager();

            // Set the limit
            if($request->request->getAlnum('amount') === 'all') {
                $data = $em->getRepository('AppBundle:Data')->getMax(-1);
            } else {
                $data = $em->getRepository('AppBundle:Data')->getMax($request->request->getInt('amount'));
            }

            // Add the header of the CSV file
            fputcsv(
                $handle,
                array_values($request->request->get('properties')),
                ';'
            );

            // Add the data queried from database
            foreach($data as $item) {
                $row = [];
                foreach($request->request->get('properties') as $key => $value) {
                    if($value === 'Sensor') {
                        $row[] = ($item->getSensor() != null) ? $item->getSensor()->__toString() : 'No sensor assigned.';
                    } else if($value === 'Time') {
                        if($item->{'get'.$value}()) {
                            $row[] = $item->{'get' . $value}()->format('H:i');
                        } else $row[] = null;
                    } else if($value === 'Date') {
                        if($item->{'get'.$value}()) {
                            $row[] = $item->{'get' . $value}()->format('d.m.Y');
                        } else $row[] = null;
                    } else $row[] = $item->{'get'.$value}(); // Use the getter by the passed form data string
                }

                fputcsv(
                    $handle, // The file pointer
                    $row,
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