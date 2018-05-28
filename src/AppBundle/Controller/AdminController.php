<?php

namespace AppBundle\Controller;

// Annotations
use DateTime;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $sensors = $em->getRepository('AppBundle:Sensor')->findAll();
        $sensorTypes = $em->getRepository('AppBundle:SensorType')->findAll();

        $recentTimestamp = (count($recentTimestamp) > 0) ? $recentTimestamp : null;

        return $this->render('AppBundle:EasyAdmin:dashboard.html.twig', [
            'dataCount' => $dataCount[0]['amount'],
            'sensorCount' => count($sensors),
            'sensors' => $sensors,
            'recentTimestamp' => $recentTimestamp[0],
            'sensorTypes' => $sensorTypes
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
        $response = new StreamedResponse();
        $response->setCallback(function() use ($request) {
            $handle = fopen('php://output', 'w+');

            $em = $this->getDoctrine()->getManager();

            $start = new \DateTime($request->request->get('startDate'));
            $end = new \DateTime($request->request->get('endDate'));
            $data = $em->getRepository('AppBundle:Data')->getLimited($start, $end);

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

    /**
     * @Route("/delete", name="delete_all")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return RedirectResponse
     */
    public function ajaxDeleteAllAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('AppBundle:Data')->findAll();

        foreach ($data as $item) {
            $em->remove($item);
        }

        $em->flush();

        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * @Route("/delete_type", name="delete_of_type")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return RedirectResponse
     */
    public function ajaxDeleteOfTypeAction(Request $request) {
        $type = $request->get('type');
        $em = $this->getDoctrine()->getManager();

        // Get all sensors by sensor type and then the corresponding data
        $sensors = $em->getRepository('AppBundle:Sensor')->findBy(['sensorType' => $type]);
        $data = $em->getRepository('AppBundle:Data')->findBy(['sensor' => $sensors]);

        foreach ($data as $item) {
            $em->remove($item);
        }

        $em->flush();

        return $this->redirectToRoute('admin_dashboard');
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
            $limitTime = date_modify(new DateTime('now', new \DateTimeZone('UTC')), '-1 hour')->format('H:i');
            $limitDate = date_modify(new DateTime('now', new \DateTimeZone('UTC')), '-1 hour')->format('d.m.Y');

            $response[] = [
                'id'   => $item['id'],
                'name' => $item['name'],
                'uuid' => $item['uuid'],
                'up'   => $item['date']->format('d.m.Y') >= $limitDate && $item['time']->format('H:i') >= $limitTime
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function graphDataAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('AppBundle:Data')->getRecentForSensor(
            [$request->request->get('attribute')],
            $request->request->get('sensor'),
            100
        );

        $response = [];

        foreach(array_reverse($result) as $element) {
            $response[] = [
                'id'   => $element->getId(),
                'date' => $element->getDate(),
                'time' => $element->getTime(),
                'value' => $element->{'get' . ucfirst($request->request->get('attribute'))}()
            ];
        }

        return new JsonResponse($response);
    }
}