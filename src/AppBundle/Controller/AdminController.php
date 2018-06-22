<?php

namespace AppBundle\Controller;

// Annotations
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdminController extends BaseAdminController {

    private $userEntities = ['Data' => ['list', 'show', 'search']];
    private $user = null;
    private $logger;

    public function __construct(TokenStorageInterface $tokenStorage, LoggerInterface $loggerInterface) {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->logger = $loggerInterface;
    }

    /**
     * @Route("/", name="admin")
     * @Route("/", name="easyadmin")
     * @Security("has_role('ROLE_USER')")
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
        if (!$this->isActionAllowed($action) || !$this->userHasPermission($request->query->get('entity'), $action)) {
            throw new ForbiddenActionException(array('action' => $action, 'entity_name' => $this->entity['name']));
        }

        return $this->executeDynamicMethod($action.'<EntityName>Action');
    }

    /**
     * Checks if user has permissions to proceed with action. Uses the $userEntities variable to determine.
     * @param $entity
     * @param $action
     * @return bool Boolean: user has permissions to view resource
     */
    protected function userHasPermission($entity, $action) {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return true;
        } else if($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            if(array_key_exists($entity, $this->userEntities) !== false) {
                if(array_search($action, $this->userEntities[$entity]) !== false) {
                    return true;
                } else return false;
            } else return false;
        } else return false;
    }

    /**
     * @Route("/dashboard", name="admin_dashboard")
     * @Security("has_role('ROLE_USER')")
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
            $sensors = $this->getSensorsFromData($data);

            // Add the header of the CSV file
            fputcsv(
                $handle,
                $this->csvHeader(array_values($request->request->get('properties')), $sensors),
                ';'
            );

            // Add the data queried from database
            foreach($data as $item) {
                fputcsv(
                    $handle, // The file pointer
                    $this->csvRow($request, $item, $sensors),
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

    private function csvHeader($properties, $sensors) {
        $result = ['Date', 'Time'];

        foreach($sensors as $sensor) {
            $result[] = $sensor->getName();
            foreach($properties as $property)
                $result[] = $property;
        }

        return $result;
    }

    private function csvRow($request, $item, $sensors) {
        $row = [$item->getDate()->format('d.m.Y'), $item->getTime()->format('H:i:s')];
        foreach($sensors as $sensor) {
            if($sensor->getId() === $item->getSensor()->getId()) {
                // Insert empty field for sensor name
                $row[] = '';
                foreach($request->request->get('properties') as $key => $value) {
                    if($value === 'Sensor') {
                        $row[] = ($item->getSensor() != null) ? $item->getSensor()->__toString() : 'No sensor assigned.';
                    } else $row[] = $item->{'get'.$value}(); // Use the getter by the passed form data string
                }
            } else {
                // Insert empty item for the sensor name
                $row[] = '';
                foreach($request->request->get('properties') as $key => $value) {
                    $row[] = '';
                }
            }
        }

        return $row;
    }

    private function getSensorsFromData($data) {
        $result = [];
        foreach($data as $item) {
            if(!array_key_exists($item->getSensor()->getId(), $result)) {
                $result[$item->getSensor()->getId()] = $item->getSensor();
            }
        }

        return $result;
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
     * @Security("has_role('ROLE_USER')")
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
                'up'   => strtotime($item['date']->format('d.m.Y')) >= strtotime($limitDate)
                            &&
                            strtotime($item['time']->format('H:i')) >= strtotime($limitTime)
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @Security("has_role('ROLE_USER')")
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

    /*
     * Data functions
     */

    protected function createDataListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null) {
        $dqlFilter = $this->getFilter();

        return parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);
    }


    protected function createDataSearchQueryBuilder($entityClass, $searchQuery, array $searchableFields, $sortField = null, $sortDirection = null, $dqlFilter = null) {
        $dqlFilter = $this->getFilter();

        return parent::createSearchQueryBuilder($entityClass, $searchQuery, $searchableFields, $sortField, $sortDirection, $dqlFilter);
    }

    /**
     * Gets filter and appends it to the existing dql filter
     * @return null|string
     */
    private function getFilter() {
        if($this->user->getRole() === 'ROLE_USER') {
            $sensors = $this->user->getSensors();

            $filter = '';

            // For each sensor add condition
            foreach($sensors as $index => $sensor) {
                // Start with the fresh condition only if first index and dqlFilter was empty.
                if($index === 0) {
                    $filter .= 'entity.sensor = '.$sensor->getId();
                } else {
                    $filter .= ' OR entity.sensor = '.$sensor->getId();
                }
            }

            return $filter;
        } else return null;
    }

    /*
     * End data functions
     */

    /*
     * User functions
     */

    /*
     * End user functions
     */
}