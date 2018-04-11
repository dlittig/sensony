<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Data;
use AppBundle\Entity\Sensor;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Yaml\Yaml;

class ApiV1Controller extends Controller {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * If sensor can trigger this action he is already authenticated.
     *
     * @Security("has_role('ROLE_SENSOR')")
     * @param Request $request
     * @param UserInterface $sensor
     * @return JsonResponse
     */
    public function dataAction(Request $request, UserInterface $sensor) {
        //if(!$this->validateStructure($request->request)) return new JsonResponse(['error' => 'Invalid data.'], 400);
        if ($sensor === null) return new JsonResponse(['error' => 'Invalid data.'], 400);

        $this->em = $this->getDoctrine()->getManager();

        $data = new Data();
        $data->setSensor($sensor);
        $this->retrieveData($data, new ParameterBag(json_decode($request->getContent(), true)));
        $this->em->persist($data);
        $this->em->flush();

        return new JsonResponse(['success' => 'true']);
    }

    /**
     * Fetches the data out of the parameter bag if available
     * @param Data $data
     * @param ParameterBag $request
     * @return Data
     */
    private function retrieveData(Data $data, ParameterBag $request) {
        $mapping = $this->processMapping($data->getSensor());

        $data->setADC0($this->fetch($request, 'adc0', $mapping));
        $data->setADC1($this->fetch($request, 'adc1', $mapping));
        $data->setADC2($this->fetch($request, 'adc2', $mapping));
        $data->setADC3($this->fetch($request, 'adc3', $mapping));
        $data->setADC4($this->fetch($request, 'adc4', $mapping));
        $data->setADC5($this->fetch($request, 'adc5', $mapping));
        $data->setADC6($this->fetch($request, 'adc6', $mapping));
        $data->setADC7($this->fetch($request, 'adc7', $mapping));
        $data->setLatitude($this->fetch($request, 'latitude', $mapping));
        $data->setLongitude($this->fetch($request, 'longitude', $mapping));
        $data->setElevation($this->fetch($request, 'elevation', $mapping));
        $data->setTemp($this->fetch($request, 'temp', $mapping));
        $data->setMoist($this->fetch($request, 'moist', $mapping));
        $data->setPressure($this->fetch($request, 'pressure', $mapping));
        $data->setSpeed($this->fetch($request, 'speed', $mapping));
        $data->setDate($this->fetch($request, 'date', $mapping));
        $data->setTime($this->fetch($request, 'time', $mapping));

        return $data;
    }

    /**
     * @param Sensor $sensor
     * @return array
     */
    private function processMapping(Sensor $sensor) {
        if($sensor->getSensorType() == null) return [];
        $mapping = $sensor->getSensorType()->getMapping();

        // Without mapping take the defaults
        if($mapping == '') return [];

        return Yaml::parse($mapping);
    }

    /**
     * @param ParameterBag $request
     * @param $key
     * @param $mapping
     * @return mixed|null
     */
    private function fetch(ParameterBag $request, $key, $mapping) {
        if(array_key_exists($key, $mapping)) {
            return $request->get($mapping[$key]);
        } else {
            // Without mapping take the defaults if the value wasnt used already
            if(!in_array($key, $mapping)) {
                return $request->get($key);
            } else return null;
        }
    }
}
