<?php

namespace App\Repository;

use App\Entity\NotificationSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class NotificationSettingsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NotificationSettings::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('n')
            ->where('n.something = :value')->setParameter('value', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
