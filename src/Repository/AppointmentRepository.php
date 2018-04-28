<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    /**
     * @param $ssn
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySsnAndTodayDay($ssn)
    {
        $query = $this->createQueryBuilder('a');
        $query
            ->where('a.applicantSsn = :ssn')->setParameter('ssn', $ssn)
            ->andWhere('a.date = :today')->setParameter('today', new \DateTime())
            ->setFirstResult(0)
            ->setMaxResults(1);

        return $query->getQuery()->getSingleResult();
    }
}
