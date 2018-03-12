<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Data;
use AppBundle\Entity\Sensor;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ApiV1Controller extends Controller {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param Request $request
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function dataAction(Request $request, LoggerInterface $logger) {
        if(!$this->validateStructure($request->request)) return new JsonResponse(['error' => 'Invalid data.'], 400);

        $this->em = $this->getDoctrine()->getManager();

        // Check for authentication
        $sensor = null;
        if(($sensor = $this->isAuthenticated($request->request)) === null) return new JsonResponse(['error' => 'Not authenticated.'], 403);

        $data = new Data();
        $data->setSensor($sensor);
        $this->retrieveData($data, $request->request);
        $this->em->persist($data);
        $this->em->flush();

        return new JsonResponse(['success' => 'true']);
    }

    /**
     * Check if the passed token is valid and correctly associated to the sensor.
     * @param ParameterBag $request
     * @return object
     */
    private function isAuthenticated(ParameterBag $request) {
        $sensor = $this->em->getRepository(Sensor::class)->findOneBy(['id' => $request->getInt('id')]);

        if($sensor != null) {
            return ($sensor->getToken() === $request->getAlnum('token')) ? $sensor : null;
        }

        return null;
    }

    /**
     * Validates of strictly required data is present.
     * @param ParameterBag $request
     * @return bool
     */
    private function validateStructure(ParameterBag $request) {
        // Check for sensor ID and token
        return
            $request->get('token') != null &&
            $request->get('id') != null &&
            is_string($request->get('token')) &&
            is_numeric($request->get('id'))
        ;
    }

    /**
     * Fetches the data out of the parameter bag if available
     * @param Data $data
     * @param ParameterBag $request
     * @return Data
     */
    private function retrieveData(Data $data, ParameterBag $request) {
        $data->setADC0($request->get('adc0'));
        $data->setADC1($request->get('adc1'));
        $data->setADC2($request->get('adc2'));
        $data->setADC3($request->get('adc3'));
        $data->setADC4($request->get('adc4'));
        $data->setADC5($request->get('adc5'));
        $data->setADC6($request->get('adc6'));
        $data->setADC7($request->get('adc7'));
        $data->setLatitude($request->get('latitude'));
        $data->setLongitude($request->get('longitude'));
        $data->setElevation($request->get('elevation'));
        $data->setTemp($request->get('temp'));
        $data->setMoist($request->get('moist'));
        $data->setPressure($request->get('pressure'));
        $data->setSpeed($request->get('speed'));
        $data->setDate($request->get('date'));
        $data->setTime($request->get('time'));

        return $data;
    }
}
