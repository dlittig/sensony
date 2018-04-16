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
     * @param UserInterface|Sensor $sensor
     * @return JsonResponse
     */
    public function dataAction(Request $request, UserInterface $sensor) {
        if ($sensor === null) return new JsonResponse(['error' => 'Invalid data.'], 400);

        $this->em = $this->getDoctrine()->getManager();

        // Update type with latest request
        $type = $sensor->getSensorType();
        if($type->getRequest() === '' || $type->getRequest() === null) {
            $type->setRequest($request->getContent());
            $this->em->persist($type);
        }

        $data = new Data();
        $data->setSensor($sensor);

        $values = json_decode($request->getContent(), true);
        $this->retrieveData($data, $values['sensordatavalues']);

        if($data->getTime() === null) {
            $data->setTime(new \DateTime('now'));
        }

        if($data->getDate() === null) {
            $data->setDate(new \DateTime('now'));
        }

        $this->em->persist($data);
        $this->em->flush();

        return new JsonResponse(['success' => 'true']);
    }

    /**
     * Fetches the data out of the parameter bag if available
     * @param Data $data
     * @param array $request
     * @return Data
     */
    private function retrieveData(Data $data, Array $request) {
        $mapping = $this->processMapping($data->getSensor());

        $this->fetch($request, $mapping, function($key, $value) use ($data) {
            switch($key) {
                case 'adc0':
                    $data->setADC0(floatval($value));
                    break;
                case 'adc1':
                    $data->setADC1(floatval($value));
                    break;
                case 'adc2':
                    $data->setADC2(floatval($value));
                    break;
                case 'adc3':
                    $data->setADC3(floatval($value));
                    break;
                case 'adc4':
                    $data->setADC4(floatval($value));
                    break;
                case 'adc5':
                    $data->setADC5(floatval($value));
                    break;
                case 'adc6':
                    $data->setADC6(floatval($value));
                    break;
                case 'adc7':
                    $data->setADC7(floatval($value));
                    break;
                case 'latitude':
                    $data->setLatitude(floatval($value));
                    break;
                case 'longitude':
                    $data->setLongitude(floatval($value));
                    break;
                case 'elevation':
                    $data->setElevation(floatval($value));
                    break;
                case 'temp':
                    $data->setTemp(floatval($value));
                    break;
                case 'moist':
                    $data->setMoist(floatval($value));
                    break;
                case 'pressure':
                    $data->setPressure(floatval($value));
                    break;
                case 'speed':
                    $data->setSpeed(floatval($value));
                    break;
                case 'humidity':
                    $data->setHumidity(floatval($value));
                    break;
                case 'sdsp1':
                    $data->setSDSP1(floatval($value));
                    break;
                case 'sdsp2':
                    $data->setSDSP2(floatval($value));
                    break;
                case 'time':
                    $data->setTime($value);
                    break;
                case 'date':
                    $data->setDate($value);
                    break;
            }
        });

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
     * Iterates through the request and calls the callback for each result
     * @param ParameterBag $request
     * @param $mapping
     * @param $callback
     * @return mixed|null
     */
    private function fetch($request, $mapping, $callback) {
        foreach($request as $item) {
            $keys = array_keys($mapping, $item['value_type']);

            // Mapping found
            if(count($keys) > 0) {
                foreach ($keys as $key) {
                    // Use mapped attribute
                    $callback($key, $item['value']);
                }
            } else {
                // No mapping found, take defaults
                $callback($item['value_type'], $item['value']);
            }
        }
    }
}
