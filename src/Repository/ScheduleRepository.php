<?php

namespace App\Repository;

use App\Entity\Gate;
use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    public function findByGateGroupByGuard(Gate $gate)
    {
        return $this->createQueryBuilder('s')
            ->where('s.gate = :gate')->setParameter('gate', $gate)
            ->groupBy('s.guard')
            ->getQuery()
            ->getResult();
    }
}
