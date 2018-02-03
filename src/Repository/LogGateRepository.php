<?php

namespace App\Repository;

use App\Entity\LogGate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Constraints\Date;

class LogGateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LogGate::class);
    }

    /**
     * @param $building
     * @param $date
     * @return array
     * @throws DBALException
     */
    public function findByDate($building, $date)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT g.name as gate, count(l.id) as scans
                FROM gate g LEFT JOIN log_gate l ON g.id = l.gate_id AND l.date = :get_date, building b 
                WHERE b.id = :building_id and b.id = g.building_id
                GROUP BY g.name ORDER BY g.id";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['building_id' => $building, 'get_date' => $date]);

        return $stmt->fetchAll();

    }

    /**
     * @param $building
     * @param $month
     * @param $year
     * @return array
     * @throws DBALException
     */
    public function findByMonth($building, $month, $year)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT l.date as date, COUNT(l.id) as scans
                FROM log_gate l, gate g
                WHERE MONTH(date) = :get_month AND YEAR(date) = :get_year AND l.gate_id = g.id AND g.building_id = :building_id 
                GROUP BY date";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['building_id' => $building, 'get_month' => $month, 'get_year' => $year]);

        return $stmt->fetchAll();
    }
}
