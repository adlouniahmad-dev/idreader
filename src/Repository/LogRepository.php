<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\Gate;
use App\Entity\Guard;
use App\Entity\Log;
use App\Entity\Office;
use App\Entity\Visitor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

        $sql = "SELECT v.id, v.first_name, v.last_name, l.date_created, 
                ABS(TIME_TO_SEC(TIMEDIFF(l.estimated_time, l.time_entered)) / 60) AS expected, 
                ABS(TIME_TO_SEC(TIMEDIFF(l.time_exit, l.time_entered)) / 60) AS realExit,
                l.date_left_from_office as officeLeft,
                l.id as logID
                FROM log l INNER JOIN visitor v ON l.visitor_id = v.id
                WHERE (l.time_exit IS NOT NULL
                AND ABS(TIMEDIFF(l.time_exit, l.time_entered)) > ADDTIME(ABS(TIMEDIFF(l.estimated_time, l.time_entered)), SEC_TO_TIME((SELECT o.suspicious_after FROM office_settings o, log ll where o.office_id = ll.office_id and ll.id = l.id) * 60)))
                OR (l.date_left_from_office IS NULL AND l.time_exit IS NOT NULL)
                ORDER BY l.id DESC";

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
     * @param Office $office
     * @return mixed
     */
    public function getVisitsToday(Office $office)
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.office = :office')->setParameter('office', $office)
            ->andWhere('l.dateCreated = :today')->setParameter('today', date_format(new \DateTime(), 'Y-m-d'))
            ->andWhere($query->expr()->isNull('l.timeExit'))
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

    /**
     * @param Office|null $office
     * @return mixed
     */
    public function getTotalVisitorsPerDay(Office $office = null)
    {
        return $this->createQueryBuilder('l')
            ->select('count(l.id)')
            ->where('l.office = :office')->setParameter('office', $office)
            ->andWhere('l.dateCreated = :today')->setParameter('today', date_format(new \DateTime(), 'Y-m-d'))
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * @param Office $office
     * @return array
     */
    public function getDoneVisitsPerDay(Office $office = null)
    {
        $query = $this->createQueryBuilder('l');
        $query
            ->select('count(l.id)')
            ->where('l.office = :office')->setParameter('office', $office)
            ->andWhere('l.dateCreated = :today')->setParameter('today', date_format(new \DateTime(), 'Y-m-d'))
            ->andWhere($query->expr()->isNotNull('l.dateLeftFromOffice'));

        return $query->getQuery()->getScalarResult();
    }

    /**
     * @param Office $office
     * @return array
     */
    public function getCountTotalVisits(Office $office = null)
    {
        $query = $this->createQueryBuilder('l');
        $query
            ->select('count(l.id)')
            ->where('l.office = :office')->setParameter('office', $office);

        return $query->getQuery()->getScalarResult();
    }

    /**
     * @param Visitor $visitor
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastLogForSpecificVisitor(Visitor $visitor)
    {
        $query = $this->createQueryBuilder('l');
        $query
            ->where('l.visitor = :visitor')->setParameter('visitor', $visitor)
            ->andWhere('l.dateCreated = :today')->setParameter('today', date_format(new \DateTime(), 'Y-m-d'))
            ->andWhere($query->expr()->isNull('l.timeExit'))
            ->orderBy('l.id', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1);

        return $query->getQuery()->getSingleResult();
    }

    /**
     * @param Building $building
     * @return mixed
     */
    public function findByBuilding(Building $building)
    {
        $query = $this->createQueryBuilder('l');
        $query
            ->leftJoin('l.office', 'o')
            ->innerJoin('o.building', 'b', 'WITH', 'b = :building')->setParameter('building', $building);

        return $query->getQuery()->getResult();
    }

    /**
     * @param $visitorName
     * @param $timeLeftFrom
     * @param $timeLeftTo
     * @param $dateFrom
     * @param $dateTo
     * @param Office $office
     * @return mixed
     */
    public function advancedSearchPremiseOwner($visitorName, $timeLeftFrom, $timeLeftTo, $dateFrom, $dateTo, Office $office)
    {
        $query = $this->createQueryBuilder('l');
        $query
            ->innerJoin('l.visitor', 'v')
            ->innerJoin('l.office', 'o', 'WITH', 'o = :office')->setParameter('office', $office)
            ->where('CONCAT(v.firstName, \' \', v.lastName) LIKE :visitorName')->setParameter('visitorName', '%' . $visitorName . '%')
            ->andWhere('l.dateLeftFromOffice BETWEEN :timeFrom AND :timeTo')
            ->orWhere($query->expr()->isNull('l.dateLeftFromOffice'))
            ->andWhere('l.dateCreated BETWEEN :dateFrom AND :dateTo')
            ->setParameter('timeFrom', $timeLeftFrom)
            ->setParameter('timeTo', $timeLeftTo)
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo);

        return $query->getQuery()->getResult();
    }

    /**
     * @param $visitorName
     * @param $dateFrom
     * @param $dateTo
     * @param $timeEnteredFrom
     * @param $timeEnteredTo
     * @param $timeExitFrom
     * @param $timeExitTo
     * @param Building|null $building
     * @param Office|null $office
     * @param Guard|null $entranceGuard
     * @param Guard|null $exitGuard
     * @param Gate|null $entranceGate
     * @param Gate|null $exitGate
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function advancedSearch($visitorName, $dateFrom, $dateTo, $timeEnteredFrom, $timeEnteredTo, $timeExitFrom, $timeExitTo,
                                   $building = null, $office = null,
                                   $entranceGuard = null, $exitGuard = null, $entranceGate = null, $exitGate = null)
    {
        $conn = $this->getEntityManager()->getConnection();
        if ($building === null) {

            $sql = "select log.id as logId, concat(visitor.first_name, ' ', visitor.last_name) as visitorName, officeBuilding.name as buildingName, officeBuilding.office_nb as officeName, logGateEntrance.name as entranceGate, logGateExit.name as exitGate, logGuardEntrance.sName as entranceGuard, logGuardExit.sName as exitGuard, log.date_created as dateCreated, log.time_entered as timeEntered, log.time_exit as timeExit
                    from log
                    left join (select log_gate.log_id, gate.name from log_gate inner join gate on log_gate.gate_id = gate.id and log_gate.status = 'entrance') logGateEntrance on log.id = logGateEntrance.log_id
                    left join (select log_gate.log_id, gate.name from log_gate inner join gate on log_gate.gate_id = gate.id and log_gate.status = 'exit') logGateExit on log.id = logGateExit.log_id
                    left join (select log_guard.log_id, concat(user.given_name, ' ', user.family_name) as sName from log_guard inner join guard on log_guard.guard_id = guard.id and log_guard.status = 'entrance', user where guard.user_id = user.id) logGuardEntrance on log.id = logGuardEntrance.log_id
                    left join (select log_guard.log_id, concat(user.given_name, ' ', user.family_name) as sName from log_guard inner join guard on log_guard.guard_id = guard.id and log_guard.status = 'exit', user where guard.user_id = user.id) logGuardExit on log.id = logGuardExit.log_id
                    inner join visitor on log.visitor_id = visitor.id and concat(visitor.first_name, ' ', visitor.last_name) LIKE :visitorName
                    inner join (select office.id as officeId, office.office_nb, building.id as buildingId, building.name from office INNER JOIN building on office.building_id = building.id) officeBuilding on log.office_id = officeBuilding.officeId 
                    where log.date_created between :dateFrom and :dateTo
                    and log.time_entered between :timeEnteredFrom and :timeEnteredTo
                    and log.time_exit between :timeExitFrom and :timeExitTo or log.time_exit is null
                    order by log.id desc";

            $stmt = $conn->prepare($sql);
            $stmt->execute(array(
                'visitorName' => '%' . $visitorName . '%',
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'timeEnteredFrom' => $timeEnteredFrom,
                'timeEnteredTo' => $timeEnteredTo,
                'timeExitFrom' => $timeExitFrom,
                'timeExitTo' => $timeExitTo
            ));

        } else {

            $sql = "select log.id as logId, concat(visitor.first_name, ' ', visitor.last_name) as visitorName, officeBuilding.name as buildingName, 
                    officeBuilding.office_nb as officeName, logGateEntrance.name as entranceGate, logGateExit.name as exitGate, logGuardEntrance.sName 
                    as entranceGuard, logGuardExit.sName as exitGuard, log.date_created as dateCreated ,log.time_entered as timeEntered, log.time_exit as timeExit
                    from log
                    inner join (select log_gate.log_id, gate.name from log_gate inner join gate on log_gate.gate_id = gate.id and log_gate.status = 'entrance'";

            if ($entranceGate == -1)
                $sql .= " and log_gate.gate_id is not null)";
            else
                $sql .= " and log_gate.gate_id = :entranceGate)";

            $sql .= " logGateEntrance on log.id = logGateEntrance.log_id
                    inner join (select log_gate.log_id, gate.name from log_gate inner join gate on log_gate.gate_id = gate.id and log_gate.status = 'exit'";

            if ($exitGate == -1)
                $sql .= " and log_gate.gate_id is not null or log_gate.gate_id is null)";
            else
                $sql .= " and log_gate.gate_id = :exitGate)";

            $sql .= " logGateExit on log.id = logGateExit.log_id    
                    inner join (select log_guard.log_id, concat(user.given_name, ' ', user.family_name) as sName from log_guard inner join guard on log_guard.guard_id = guard.id and log_guard.status = 'entrance'";

            if ($entranceGuard == -1)
                $sql .= " and log_guard.guard_id is not null";
            else
                $sql .= " and log_guard.guard_id = :entranceGuard";

            $sql .= ", user where guard.user_id = user.id) logGuardEntrance on log.id = logGuardEntrance.log_id
                    inner join (select log_guard.log_id, concat(user.given_name, ' ', user.family_name) as sName from log_guard inner join guard on log_guard.guard_id = guard.id and log_guard.status = 'exit'";

            if ($exitGuard == -1)
                $sql .= " and log_guard.guard_id is not null or log_guard.guard_id is null";
            else
                $sql .= " and log_guard.guard_id = :exitGuard";

            $sql .= ", user where guard.user_id = user.id) logGuardExit on log.id = logGuardExit.log_id
                    inner join visitor on log.visitor_id = visitor.id and concat(visitor.first_name, ' ', visitor.last_name) LIKE :visitorName
                    inner join (select office.id as officeId, office.office_nb, building.id as buildingId, building.name from office INNER JOIN building on office.building_id = building.id and office.building_id = :building";

            if ($office == -1)
                $sql .= " and office.id is not null";
            else
                $sql .= " and office.id = :office";

            $sql .= ") officeBuilding on log.office_id = officeBuilding.officeId 
                    where log.date_created between :dateFrom and :dateTo
                    and log.time_entered between :timeEnteredFrom and :timeEnteredTo
                    and log.time_exit between :timeExitFrom and :timeExitTo or log.time_exit is null
                    order by log.id desc";

            $stmt = $conn->prepare($sql);

            $tokens = array(
                'visitorName' => '%' . $visitorName . '%',
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'timeEnteredFrom' => $timeEnteredFrom,
                'timeEnteredTo' => $timeEnteredTo,
                'timeExitFrom' => $timeExitFrom,
                'timeExitTo' => $timeExitTo,
                'building' => $building,
            );

            if ($entranceGate != -1)
                $tokens['entranceGate'] = $entranceGate;

            if ($exitGate != -1)
                $tokens['exitGate'] = $exitGate;

            if ($entranceGuard != -1)
                $tokens['entranceGuard'] = $entranceGuard;

            if ($exitGuard != -1)
                $tokens['exitGuard'] = $exitGuard;

            if ($office != -1)
                $tokens['office'] = $office;

            $stmt->execute($tokens);
        }

        return $stmt->fetchAll();

    }
}
