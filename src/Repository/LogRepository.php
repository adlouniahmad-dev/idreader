<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Log::class);
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getSuspiciousVisits()
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT v.id, v.first_name, v.middle_name, v.last_name, l.date_created, 
                TIME_TO_SEC(TIMEDIFF(l.estimated_time, l.time_entered)) / 60 AS expected, 
                TIME_TO_SEC(TIMEDIFF(l.time_exit, l.time_entered)) / 60 AS realExit
                FROM log l INNER JOIN visitor v ON l.visitor_id = v.id
                WHERE l.time_exit IS NOT NULL
                AND TIMEDIFF(l.time_exit, l.time_entered) > TIMEDIFF(l.estimated_time, l.time_entered)";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

}
