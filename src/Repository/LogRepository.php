<?php

namespace App\Repository;

use App\Entity\Log;
use App\Entity\Office;
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

    /**
     * @param Office|null $office
     * @return mixed
     */
    public function getVisitsOnPageLoad(Office $office = null)
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.office = :office')->setParameter('office', $office)
            ->andWhere('l.dateCreated = :today')->setParameter('today', date_format(new \DateTime(), 'Y-m-d'))
            ->andWhere($query->expr()->isNull('l.dateLeftFromOffice'))
            ->orderBy('l.dateCreated', 'ASC');

        return $query->getQuery()->getResult();
    }

    /**
     * @param Office|null $office
     * @param $lastLogId
     * @return mixed
     */
    public function getVisitsOnNewRecord(Office $office = null, $lastLogId)
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.office = :office')->setParameter('office', $office)
            ->andWhere('l.dateCreated = :today')->setParameter('today', date_format(new \DateTime(), 'Y-m-d'))
            ->andWhere($query->expr()->isNull('l.dateLeftFromOffice'))
            ->andWhere('l.id > :lastLogId')->setParameter('lastLogId', $lastLogId)
            ->orderBy('l.dateCreated', 'ASC');

        return $query->getQuery()->getResult();
    }

}
