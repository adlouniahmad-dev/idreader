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

        $sql = "select g.name as gate, count(lg.id) as scans
                from gate g 
                left join 
                (select log_gate.id, log_gate.gate_id from log_gate, log WHERE log_gate.log_id = log.id and log.date_created = :get_date) as lg 
                on g.id = lg.gate_id,
                building b
                where b.id = :building_id and b.id = g.building_id
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

        $sql = "SELECT l.date_created, count(lg.id) as scans
                from log_gate lg, log l, gate g
                WHERE MONTH(l.date_created) = :get_month
                and YEAR(l.date_created) = :get_year
                and lg.log_id = l.id
                and lg.gate_id = g.id
                and g.building_id = :building_id
                GROUP BY l.date_created";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['building_id' => $building, 'get_month' => $month, 'get_year' => $year]);

        return $stmt->fetchAll();
    }
}
