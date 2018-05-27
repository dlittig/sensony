<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Sensor;

/**
 * DataRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DataRepository extends \Doctrine\ORM\EntityRepository {
    public function getDataCount() {
        return $this->createQueryBuilder('data')
            ->select('COUNT(data.id) as amount')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $items
     * @param $sensorId
     * @param $max
     * @return array
     */
    public function getRecentForSensor(array $items, $sensorId, $max) {
        $query = $this->createQueryBuilder('data')
            ->select('data')
            ->where('data.sensor = :id')
            ->setParameter('id', $sensorId)
            ->setMaxResults($max)
            ->orderBy('data.id', 'DESC');

        foreach($items as $item) {
            $query->andWhere('data.'. $item .' IS NOT NULL');
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param \DateTime $dateTimeStart
     * @param \DateTime $dateTimeEnd
     * @return array
     */
    public function getLimited(\DateTime $dateTimeStart, \DateTime $dateTimeEnd) {
        return $this->createQueryBuilder('data')
            ->select('data')
            ->where('data.time BETWEEN :timeStart AND :timeEnd')
            ->andWhere('data.date BETWEEN :dateStart AND :dateEnd')
            ->setParameters([
                'timeStart' => $dateTimeStart->format('H:i'),
                'dateStart' => $dateTimeStart->format('Y-m-d'),
                'timeEnd' => $dateTimeEnd->format('H:i'),
                'dateEnd' => $dateTimeEnd->format('Y-m-d')
            ])
            ->orderBy('data.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function getSensorStatus() {
        return $this->createQueryBuilder('data')
            ->join('data.sensor', 'sensor')
            ->addSelect('sensor.id, sensor.name, sensor.uuid, data.date, data.time')
            ->where('data.date IS NOT NULL')
            ->andWhere('data.time IS NOT NULL')
            ->orderBy('sensor.name', 'ASC')
            ->groupBy('sensor.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function getRecentDateTime() {
        return $this->createQueryBuilder('data')
            ->select('data.time, data.date')
            ->setMaxResults(1)
            ->orderBy('data.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
